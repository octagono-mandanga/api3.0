<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResendService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SolicitudActivacionController extends Controller
{
    // Tiempo de vida del código de verificación (en minutos)
    const CODIGO_EXPIRACION_MINUTOS = 10;

    protected ResendService $resendService;
    protected TwilioService $twilioService;

    public function __construct(ResendService $resendService, TwilioService $twilioService)
    {
        $this->resendService = $resendService;
        $this->twilioService = $twilioService;
    }

    /**
     * Obtiene los datos públicos de una solicitud para pre-llenar la página de configuración.
     * No expone códigos de verificación.
     */
    public function obtener(Request $request, $id)
    {
        $solicitud = DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->first();

        if (!$solicitud) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El enlace de configuración no es válido o ha expirado.',
            ], 404);
        }

        // Si la solicitud aún no completó la verificación, no se puede configurar
        if ($solicitud->estado !== 'completada') {
            return response()->json([
                'status'  => 'error',
                'code'    => 'NO_COMPLETADA',
                'message' => 'La verificación de identidad no ha sido completada. Complete primero el proceso de activación.',
            ], 403);
        }

        // Si ya se hizo la configuración inicial (institucion_id ya fue asignado)
        if ($solicitud->institucion_id) {
            return response()->json([
                'status'       => 'ya_configurado',
                'message'      => 'La configuración inicial ya fue completada para esta solicitud.',
                'institucion'  => $solicitud->nombre_institucion,
                'completed_at' => $solicitud->completed_at,
            ], 200);
        }

        // Devolver datos seguros para pre-llenar el formulario
        return response()->json([
            'status'             => 'disponible',
            'nombre_institucion' => $solicitud->nombre_institucion,
            'nombre_responsable' => $solicitud->nombre_responsable,
            'correo'             => $solicitud->correo,
            'telefono'           => $solicitud->telefono,
            'niveles'            => [], // Se leerán de los datos almacenados si aplica
            'completed_at'       => $solicitud->completed_at,
        ]);
    }

    /**
     * Crea una nueva solicitud de activación institucional.
     * Paso 1: Registra datos y envía código por email.
     */
    public function crear(Request $request)
    {
        // Validación de honeypot (si el bot llenó campos ocultos)
        if ($this->detectarBot($request)) {
            // Simular respuesta exitosa para confundir al bot
            return response()->json([
                'status' => 'success',
                'message' => 'Solicitud creada correctamente.',
                'solicitud_id' => Str::uuid(),
            ], 201);
        }

        $validated = $request->validate([
            'nombre_institucion' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'telefono' => 'required|string|max:20',
            'nombre_responsable' => 'required|string|max:255',
            'documento' => 'required|string|max:20',
        ]);

        try {
            // Verificar si ya existe una solicitud activa para este correo (menos de 1 hora)
            $solicitudExistente = DB::table('core.solicitudes_activacion')
                ->where('correo', $validated['correo'])
                ->whereIn('estado', ['pendiente_email', 'pendiente_sms'])
                ->where('created_at', '>=', now()->subHour())
                ->first();

            if ($solicitudExistente) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'SOLICITUD_EN_PROCESO',
                    'message' => 'Ya existe una solicitud en proceso para este correo. Revise su bandeja de entrada o espere 1 hora para intentar nuevamente.',
                    'solicitud_id' => $solicitudExistente->id,
                ], 409);
            }

            // Generar código de verificación de 6 dígitos
            $codigoEmail = $this->generarCodigo();

            // Generar UUID para la solicitud (insertGetId no funciona con UUID como PK)
            $solicitudId = (string) Str::uuid();

            // Crear solicitud en la base de datos
            DB::table('core.solicitudes_activacion')->insert([
                'id'                 => $solicitudId,
                'nombre_institucion' => $validated['nombre_institucion'],
                'correo'             => $validated['correo'],
                'telefono'           => $validated['telefono'],
                'nombre_responsable' => $validated['nombre_responsable'],
                'documento'          => $validated['documento'],
                'codigo_email'       => $codigoEmail,
                'email_verificado'   => false,
                'sms_verificado'     => false,
                'estado'             => 'pendiente_email',
                'ip_origen'          => $request->ip(),
                'user_agent'         => $request->userAgent(),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // Enviar código por email
            $this->enviarCodigoEmail($validated['correo'], $codigoEmail, $validated['nombre_responsable']);

            return response()->json([
                'status'      => 'success',
                'message'     => 'Solicitud creada. Revise su correo electrónico.',
                'solicitud_id' => $solicitudId,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al crear solicitud de activación', [
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud. Intente nuevamente.',
            ], 500);
        }
    }

    /**
     * Verifica el código enviado por email.
     * Paso 2: Valida código, verifica expiración y envía SMS.
     */
    public function verificarEmail(Request $request, $id)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|size:6',
        ]);

        $solicitud = DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->where('estado', 'pendiente_email')
            ->first();

        if (!$solicitud) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solicitud no encontrada o ya fue procesada.',
            ], 404);
        }

        // Verificar si el código ha expirado (10 minutos desde el último updated_at)
        $tiempoTranscurrido = now()->diffInMinutes($solicitud->updated_at);
        if ($tiempoTranscurrido >= self::CODIGO_EXPIRACION_MINUTOS) {
            return response()->json([
                'status' => 'error',
                'code' => 'CODE_EXPIRED',
                'message' => 'El código ha expirado. Solicite un nuevo código.',
            ], 410);
        }

        // Verificar intentos de código (máximo 5)
        $intentosKey = "solicitud:email_intentos:{$id}";
        $intentos = Cache::get($intentosKey, 0);

        if ($intentos >= 5) {
            return response()->json([
                'status' => 'error',
                'code' => 'MAX_ATTEMPTS',
                'message' => 'Máximo de intentos alcanzado. Solicite un nuevo código.',
            ], 429);
        }

        if ($solicitud->codigo_email !== $validated['codigo']) {
            Cache::put($intentosKey, $intentos + 1, 300); // 5 minutos

            return response()->json([
                'status' => 'error',
                'message' => 'Código incorrecto. Verifique e intente nuevamente.',
                'intentos_restantes' => 5 - ($intentos + 1),
            ], 400);
        }

        // Código correcto - generar código SMS
        $codigoSms = $this->generarCodigo(4);

        DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->update([
                'email_verificado' => true,
                'codigo_sms' => $codigoSms,
                'estado' => 'pendiente_sms',
                'updated_at' => now(),
            ]);

        // Enviar código por SMS
        $this->enviarCodigoSms($solicitud->telefono, $codigoSms);

        // Limpiar intentos
        Cache::forget($intentosKey);

        return response()->json([
            'status' => 'success',
            'message' => 'Email verificado. Se envió un código a su teléfono.',
        ]);
    }

    /**
     * Verifica el código enviado por SMS.
     * Paso 3: Valida código, verifica expiración y activa la cuenta.
     */
    public function verificarSms(Request $request, $id)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|size:4',
        ]);

        $solicitud = DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->where('estado', 'pendiente_sms')
            ->first();

        if (!$solicitud) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solicitud no encontrada o ya fue procesada.',
            ], 404);
        }

        // Verificar si el código SMS ha expirado (10 minutos desde el último updated_at)
        $tiempoTranscurrido = now()->diffInMinutes($solicitud->updated_at);
        if ($tiempoTranscurrido >= self::CODIGO_EXPIRACION_MINUTOS) {
            return response()->json([
                'status' => 'error',
                'code' => 'CODE_EXPIRED',
                'message' => 'El código ha expirado. Solicite un nuevo código.',
            ], 410);
        }

        // Verificar intentos
        $intentosKey = "solicitud:sms_intentos:{$id}";
        $intentos = Cache::get($intentosKey, 0);

        if ($intentos >= 5) {
            return response()->json([
                'status' => 'error',
                'code' => 'MAX_ATTEMPTS',
                'message' => 'Máximo de intentos alcanzado. Solicite un nuevo código.',
            ], 429);
        }

        if ($solicitud->codigo_sms !== $validated['codigo']) {
            Cache::put($intentosKey, $intentos + 1, 300);

            return response()->json([
                'status' => 'error',
                'message' => 'Código incorrecto. Verifique e intente nuevamente.',
                'intentos_restantes' => 5 - ($intentos + 1),
            ], 400);
        }

        // Código correcto - activar solicitud
        DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->update([
                'sms_verificado' => true,
                'estado' => 'completada',
                'completed_at' => now(),
                'updated_at' => now(),
            ]);

        Cache::forget($intentosKey);

        // Enviar email de notificación: cuenta creada por facilitador
        $frontendUrl = config('app.frontend_url', 'https://app.octagono.co');
        $urlConfiguracion = "{$frontendUrl}/configuracion/{$id}";

        $this->resendService->enviarNotificacionCuentaCreada(
            $solicitud->correo,
            $solicitud->nombre_institucion,
            $solicitud->nombre_responsable,
            $urlConfiguracion,
            $frontendUrl
        );

        return response()->json([
            'status'  => 'success',
            'message' => '¡Activación completada exitosamente! Recibirá un correo con los próximos pasos.',
        ]);

    }

    /**
     * Reenvía el código de verificación por email (genera nuevo código y reinicia el timer).
     */
    public function reenviarCodigoEmail(Request $request, $id)
    {
        $solicitud = DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->whereIn('estado', ['pendiente_email', 'pendiente_sms'])
            ->first();

        if (!$solicitud) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solicitud no encontrada.',
            ], 404);
        }

        // Nuevo código — el updated_at se renueva, reiniciando el timer de expiración
        $codigoEmail = $this->generarCodigo();

        DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->update([
                'codigo_email' => $codigoEmail,
                'estado' => 'pendiente_email',
                'updated_at' => now(),
            ]);

        // Limpiar intentos fallidos al reenviar
        Cache::forget("solicitud:email_intentos:{$id}");

        $this->enviarCodigoEmail($solicitud->correo, $codigoEmail, $solicitud->nombre_responsable);

        return response()->json([
            'status' => 'success',
            'message' => 'Código reenviado exitosamente.',
        ]);
    }

    /**
     * Reenvía el código de verificación por SMS (genera nuevo código y reinicia el timer).
     */
    public function reenviarCodigoSms(Request $request, $id)
    {
        $solicitud = DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->where('estado', 'pendiente_sms')
            ->first();

        if (!$solicitud) {
            return response()->json([
                'status' => 'error',
                'message' => 'Solicitud no encontrada o no está en la etapa correcta.',
            ], 404);
        }

        // Nuevo código — el updated_at se renueva, reiniciando el timer de expiración
        $codigoSms = $this->generarCodigo(4);

        DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->update([
                'codigo_sms' => $codigoSms,
                'updated_at' => now(),
            ]);

        // Limpiar intentos fallidos al reenviar
        Cache::forget("solicitud:sms_intentos:{$id}");

        $this->enviarCodigoSms($solicitud->telefono, $codigoSms);

        return response()->json([
            'status' => 'success',
            'message' => 'Código SMS reenviado exitosamente.',
        ]);
    }

    /**
     * Detecta si la solicitud proviene de un bot.
     */
    protected function detectarBot(Request $request): bool
    {
        // Campos honeypot - si tienen valor, es un bot
        $honeypotFields = ['website', '_gotcha', 'url', 'fax', 'address2'];

        foreach ($honeypotFields as $field) {
            if ($request->filled($field)) {
                Log::warning('Bot detectado por honeypot', [
                    'ip' => $request->ip(),
                    'field' => $field,
                    'value' => $request->input($field),
                ]);
                return true;
            }
        }

        // Verificar tiempo mínimo de llenado (menos de 3 segundos = bot)
        if ($request->has('_timestamp')) {
            $formTime = (int) $request->input('_timestamp');
            $currentTime = time() * 1000;
            $elapsedMs = $currentTime - $formTime;

            if ($elapsedMs < 3000) { // Menos de 3 segundos
                Log::warning('Bot detectado por tiempo', [
                    'ip' => $request->ip(),
                    'elapsed_ms' => $elapsedMs,
                ]);
                return true;
            }
        }

        return false;
    }

    /**
     * Genera un código numérico aleatorio.
     */
    protected function generarCodigo(int $length = 6): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }

    /**
     * Envía el código de verificación por email.
     */
    protected function enviarCodigoEmail(string $correo, string $codigo, string $nombre): void
    {
        $enviado = $this->resendService->enviarCodigoVerificacion($correo, $codigo, $nombre);

        if (!$enviado) {
            Log::warning('Fallback: Email no enviado, código guardado en BD', [
                'correo' => $correo,
            ]);
        }
    }

    /**
     * Envía el código de verificación por SMS.
     */
    protected function enviarCodigoSms(string $telefono, string $codigo): void
    {
        $enviado = $this->twilioService->enviarCodigoVerificacion($telefono, $codigo);

        if (!$enviado) {
            Log::warning('Fallback: SMS no enviado, código guardado en BD', [
                'telefono' => substr($telefono, 0, 3) . '****',
            ]);
        }
    }
}

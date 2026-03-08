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
    protected ResendService $resendService;
    protected TwilioService $twilioService;

    public function __construct(ResendService $resendService, TwilioService $twilioService)
    {
        $this->resendService = $resendService;
        $this->twilioService = $twilioService;
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
            // Generar código de verificación de 6 dígitos
            $codigoEmail = $this->generarCodigo();

            // Crear solicitud en la base de datos
            $solicitudId = DB::table('core.solicitudes_activacion')->insertGetId([
                'nombre_institucion' => $validated['nombre_institucion'],
                'correo' => $validated['correo'],
                'telefono' => $validated['telefono'],
                'nombre_responsable' => $validated['nombre_responsable'],
                'documento' => $validated['documento'],
                'codigo_email' => $codigoEmail,
                'email_verificado' => false,
                'sms_verificado' => false,
                'estado' => 'pendiente_email',
                'ip_origen' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Enviar código por email
            $this->enviarCodigoEmail($validated['correo'], $codigoEmail, $validated['nombre_responsable']);

            return response()->json([
                'status' => 'success',
                'message' => 'Solicitud creada. Revise su correo electrónico.',
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
     * Paso 2: Valida código y envía SMS.
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
     * Paso 3: Valida código y activa la cuenta.
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

        return response()->json([
            'status' => 'success',
            'message' => '¡Activación completada exitosamente!',
            'redirect_url' => "/configuracion/{$id}",
        ]);
    }

    /**
     * Reenvía el código de verificación por email.
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

        // Nuevo código
        $codigoEmail = $this->generarCodigo();

        DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->update([
                'codigo_email' => $codigoEmail,
                'updated_at' => now(),
            ]);

        $this->enviarCodigoEmail($solicitud->correo, $codigoEmail, $solicitud->nombre_responsable);

        return response()->json([
            'status' => 'success',
            'message' => 'Código reenviado exitosamente.',
        ]);
    }

    /**
     * Reenvía el código de verificación por SMS.
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

        // Nuevo código
        $codigoSms = $this->generarCodigo(4);

        DB::table('core.solicitudes_activacion')
            ->where('id', $id)
            ->update([
                'codigo_sms' => $codigoSms,
                'updated_at' => now(),
            ]);

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

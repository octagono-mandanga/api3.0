<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Usuario;
use App\Services\ResendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    protected ResendService $resendService;

    public function __construct(ResendService $resendService)
    {
        $this->resendService = $resendService;
    }

    /**
     * Paso 1: Solicitar recuperación de contraseña.
     * Envía un token de 4 dígitos al correo si existe.
     */
    public function requestReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));

        $user = Usuario::where('email', $email)
            ->where('estado', 'activo')
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'No existe una cuenta activa asociada a este correo electrónico.'
            ], 404);
        }

        // Generar token de 4 dígitos
        $token = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Guardar o actualizar en auth.password_resets
        DB::table('auth.password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Enviar email con el token
        $nombreCompleto = trim("{$user->primer_nombre} {$user->primer_apellido}");
        $this->resendService->enviarCodigoRecuperacion($email, $token, $nombreCompleto);

        Log::info('Password reset requested', ['email' => $email]);

        return response()->json([
            'message' => 'Se ha enviado un código de recuperación a tu correo electrónico.'
        ]);
    }

    /**
     * Paso 2: Verificar el token de 4 dígitos.
     * Retorna un reset_token temporal para el paso 3.
     */
    public function verifyToken(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|string|size:4',
        ]);

        $email = strtolower(trim($request->email));

        $record = DB::table('auth.password_resets')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'No se encontró una solicitud de recuperación para este correo.'
            ], 404);
        }

        // Verificar expiración (10 minutos)
        if (now()->diffInMinutes($record->created_at) > 10) {
            DB::table('auth.password_resets')->where('email', $email)->delete();
            return response()->json([
                'message' => 'El código ha expirado. Solicita uno nuevo.'
            ], 410);
        }

        // Verificar token
        if (!Hash::check($request->code, $record->token)) {
            return response()->json([
                'message' => 'El código ingresado es incorrecto.'
            ], 422);
        }

        // Generar un reset_token temporal (UUID) para el paso 3
        $resetToken = Str::uuid()->toString();

        DB::table('auth.password_resets')
            ->where('email', $email)
            ->update(['token' => Hash::make($resetToken), 'created_at' => now()]);

        return response()->json([
            'message'     => 'Código verificado correctamente.',
            'reset_token' => $resetToken,
        ]);
    }

    /**
     * Paso 3: Establecer la nueva contraseña.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'reset_token' => 'required|string',
            'password'    => 'required|string|min:8|confirmed',
        ]);

        $email = strtolower(trim($request->email));

        $record = DB::table('auth.password_resets')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return response()->json([
                'message' => 'No se encontró una solicitud de recuperación válida.'
            ], 404);
        }

        // Verificar expiración (10 minutos desde verificación)
        if (now()->diffInMinutes($record->created_at) > 10) {
            DB::table('auth.password_resets')->where('email', $email)->delete();
            return response()->json([
                'message' => 'La sesión de recuperación ha expirado. Inicia el proceso nuevamente.'
            ], 410);
        }

        // Verificar reset_token
        if (!Hash::check($request->reset_token, $record->token)) {
            return response()->json([
                'message' => 'Token de recuperación inválido.'
            ], 422);
        }

        // Actualizar contraseña
        $user = Usuario::where('email', $email)->where('estado', 'activo')->first();

        if (!$user) {
            return response()->json([
                'message' => 'No se encontró la cuenta de usuario.'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar el registro de password_resets
        DB::table('auth.password_resets')->where('email', $email)->delete();

        // Revocar todos los tokens activos del usuario
        $user->tokens()->delete();

        Log::info('Password reset completed', ['email' => $email]);

        return response()->json([
            'message' => 'Tu contraseña ha sido actualizada exitosamente. Ya puedes iniciar sesión.'
        ]);
    }
}

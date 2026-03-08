<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleSolicitudes
{
    /**
     * Limita las solicitudes de activación institucional.
     *
     * Configuración:
     * - 5 intentos por minuto por IP para solicitudes
     * - 3 intentos por minuto por IP para verificación de códigos
     * - 10 intentos por hora por IP para el flujo completo
     */
    public function handle(Request $request, Closure $next, string $type = 'general'): Response
    {
        $ip = $request->ip();
        $key = $this->resolveKey($ip, $type);
        $maxAttempts = $this->getMaxAttempts($type);
        $decayMinutes = $this->getDecayMinutes($type);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'status' => 'error',
                'code' => 'TOO_MANY_REQUESTS',
                'message' => 'Demasiadas solicitudes. Por favor espere antes de intentar nuevamente.',
                'retry_after' => $seconds,
                'retry_after_human' => $this->formatSeconds($seconds),
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Agregar headers de rate limit info
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $maxAttempts));

        return $response;
    }

    /**
     * Genera la clave única para el rate limiter.
     */
    protected function resolveKey(string $ip, string $type): string
    {
        return "solicitud:{$type}:{$ip}";
    }

    /**
     * Obtiene el máximo de intentos según el tipo.
     */
    protected function getMaxAttempts(string $type): int
    {
        return match ($type) {
            'codigo' => 5,          // 5 intentos de verificación por minuto
            'reenviar' => 3,        // 3 reenvíos de código por minuto
            'crear' => 3,           // 3 creaciones de solicitud por minuto
            'general' => 30,        // 30 requests generales por minuto
            default => 10,
        };
    }

    /**
     * Obtiene el tiempo de decay en minutos según el tipo.
     */
    protected function getDecayMinutes(string $type): int
    {
        return match ($type) {
            'codigo' => 1,          // Reset cada minuto
            'reenviar' => 5,        // Reset cada 5 minutos
            'crear' => 60,          // Reset cada hora
            'general' => 1,         // Reset cada minuto
            default => 1,
        };
    }

    /**
     * Formatea segundos a formato legible.
     */
    protected function formatSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds} segundos";
        }

        $minutes = ceil($seconds / 60);
        return "{$minutes} minuto" . ($minutes > 1 ? 's' : '');
    }
}

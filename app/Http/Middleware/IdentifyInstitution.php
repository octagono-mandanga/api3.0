<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Institution;
use Symfony\Component\HttpFoundation\Response;

class IdentifyInstitution
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Obtener el dominio (ej. platform.mandanga.co)
        $host = $request->getHost();

        // 2. Buscar en la tabla auth.institutions
        $institution = Institution::where('website_url', $host)
            ->where('is_active', true)
            ->first();

        // 3. Validar si existe
        if (!$institution) {
            return response()->json([
                'message' => 'Institución no identificada o inactiva.',
                'detected_host' => $host
            ], 400);
        }

        // 4. Compartir la institución con toda la aplicación
        config(['app.current_institution' => $institution]);

        return $next($request);
    }
}

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

        // 3. Validar si existe (Silencioso para permitir Root)
        if ($institution) {
            config(['app.current_institution' => $institution]);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Institucion;
use Symfony\Component\HttpFoundation\Response;

class IdentifyInstitution
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();


        $institucion = Institucion::where('website_url', $host)
            ->where('status', 'ACTIVE')
            ->first();


        if ($institucion) {
            config(['app.current_institution' => $institucion]);
        }

        return $next($request);
    }
}

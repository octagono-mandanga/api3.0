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
        $host = $request->getHost();

     
        $institution = Institution::where('website_url', $host)
            ->where('status', 'ACTIVE')
            ->first();

        
        if ($institution) {
            config(['app.current_institution' => $institution]);
        }

        return $next($request);
    }
}

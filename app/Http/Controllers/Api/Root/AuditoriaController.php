<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\Auditoria\RegistroAcceso;
use App\Models\Auditoria\SesionActiva;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    // Ver quiénes están conectados ahora mismo
    public function getOnlineUsers()
    {
        return SesionActiva::where('is_online', true)
            ->with(['usuario:id,first_name,email'])
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    // Ver historial de intentos de acceso
    public function getAccessLogs(Request $request)
    {
        return RegistroAcceso::with('usuario:id,first_name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}

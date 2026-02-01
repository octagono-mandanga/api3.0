<?php

namespace App\Http\Controllers\Api\Root;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use App\Models\ActiveSession;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    // Ver quiénes están conectados ahora mismo
    public function getOnlineUsers()
    {
        return ActiveSession::where('is_online', true)
            ->with(['user:id,first_name,email'])
            ->orderBy('last_activity', 'desc')
            ->get();
    }

    // Ver historial de intentos de acceso
    public function getAccessLogs(Request $request)
    {
        return AccessLog::with('user:id,first_name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
}

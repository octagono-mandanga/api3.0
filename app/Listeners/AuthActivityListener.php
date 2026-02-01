<?php

namespace App\Listeners;

use App\Models\AccessLog;
use App\Models\ActiveSession;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Request;

class AuthActivityListener
{
    public function handle($event): void
    {
        if ($event instanceof Login) {
            $this->logSuccess($event);
        } elseif ($event instanceof Failed) {
            $this->logFailure($event);
        }
    }

    private function logSuccess(Login $event)
    {
        AccessLog::create([
            'user_id' => $event->user->id,
            'institution_id' => config('app.current_institution')->id ?? null,
            'event_type' => 'LOGIN_SUCCESS',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status_code' => 200
        ]);

        // Actualizar o crear sesión activa
        ActiveSession::updateOrCreate(
            ['user_id' => $event->user->id],
            [
                'ip_address' => Request::ip(),
                'last_activity' => now(),
                'is_online' => true
            ]
        );
    }

    private function logFailure(Failed $event)
    {
        AccessLog::create([
            'event_type' => 'LOGIN_FAILED',
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'status_code' => 401
        ]);
    }
}

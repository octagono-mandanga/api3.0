<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Listeners\AuthActivityListener;
use Illuminate\Support\Facades\Event;
use GuzzleHttp\Client;
use Laravel\Sanctum\Sanctum;
use App\Models\Auth\PersonalAccessToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Deshabilitar verificación SSL de Guzzle en desarrollo local (Windows)
        if ($this->app->environment('local')) {
            $this->app->bind(Client::class, function () {
                return new Client(['verify' => false]);
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Usar modelo personalizado para tokens de Sanctum (tabla auth.personal_access_tokens)
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

	Event::listen(Login::class, AuthActivityListener::class);
	Event::listen(Failed::class, AuthActivityListener::class);
    }
}

<?php

namespace Database\Factories\Auditoria;

use App\Models\Auditoria\RegistroAcceso;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistroAccesoFactory extends Factory
{
    protected $model = RegistroAcceso::class;

    public function definition(): array
    {
        $tiposEvento = ['login', 'logout', 'login_fallido', 'token_refresh', 'password_reset', 'password_change', 'two_factor_auth'];
        $metodosAuth = ['password', 'google', 'microsoft', 'facebook', 'token', 'two_factor'];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
        ];

        $tipoEvento = fake('es_ES')->randomElement($tiposEvento);
        $exitoso = $tipoEvento !== 'login_fallido';

        return [
            'usuario_id' => Usuario::factory(),
            'tipo_evento' => $tipoEvento,
            'exitoso' => $exitoso,
            'ip' => fake('es_ES')->ipv4(),
            'user_agent' => fake('es_ES')->randomElement($userAgents),
            'metodo_auth' => fake('es_ES')->randomElement($metodosAuth),
            'detalles' => $this->generarDetalles($tipoEvento, $exitoso),
        ];
    }

    protected function generarDetalles(string $tipoEvento, bool $exitoso): ?array
    {
        if ($tipoEvento === 'login_fallido') {
            return [
                'razon' => fake('es_ES')->randomElement([
                    'Contraseña incorrecta',
                    'Usuario no encontrado',
                    'Cuenta bloqueada',
                    'Demasiados intentos fallidos',
                    'Token inválido',
                ]),
                'intentos' => fake('es_ES')->numberBetween(1, 5),
            ];
        }

        if ($tipoEvento === 'password_reset') {
            return [
                'metodo' => fake('es_ES')->randomElement(['email', 'sms']),
                'solicitado_en' => fake('es_ES')->dateTimeBetween('-1 hour', 'now')->format('Y-m-d H:i:s'),
            ];
        }

        if ($tipoEvento === 'two_factor_auth') {
            return [
                'metodo_2fa' => fake('es_ES')->randomElement(['app', 'sms', 'email']),
                'verificado' => $exitoso,
            ];
        }

        return null;
    }

    public function login(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_evento' => 'login',
            'exitoso' => true,
            'detalles' => null,
        ]);
    }

    public function logout(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_evento' => 'logout',
            'exitoso' => true,
            'detalles' => null,
        ]);
    }

    public function loginFallido(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_evento' => 'login_fallido',
            'exitoso' => false,
            'detalles' => [
                'razon' => fake('es_ES')->randomElement(['Contraseña incorrecta', 'Usuario no encontrado', 'Cuenta bloqueada']),
                'intentos' => fake('es_ES')->numberBetween(1, 5),
            ],
        ]);
    }

    public function exitoso(): static
    {
        return $this->state(fn (array $attributes) => [
            'exitoso' => true,
        ]);
    }

    public function fallido(): static
    {
        return $this->state(fn (array $attributes) => [
            'exitoso' => false,
            'detalles' => ['razon' => 'Error en la autenticación'],
        ]);
    }

    public function conPassword(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_auth' => 'password',
        ]);
    }

    public function conGoogle(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_auth' => 'google',
        ]);
    }

    public function conMicrosoft(): static
    {
        return $this->state(fn (array $attributes) => [
            'metodo_auth' => 'microsoft',
        ]);
    }

    public function passwordReset(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_evento' => 'password_reset',
            'exitoso' => true,
            'detalles' => [
                'metodo' => 'email',
                'solicitado_en' => now()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    public function twoFactorAuth(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_evento' => 'two_factor_auth',
            'exitoso' => true,
            'detalles' => [
                'metodo_2fa' => fake('es_ES')->randomElement(['app', 'sms', 'email']),
                'verificado' => true,
            ],
        ]);
    }
}

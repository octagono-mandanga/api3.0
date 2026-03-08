<?php

namespace Database\Factories\Auditoria;

use App\Models\Auditoria\SesionActiva;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SesionActivaFactory extends Factory
{
    protected $model = SesionActiva::class;

    public function definition(): array
    {
        $dispositivos = [
            'Windows - Chrome',
            'Windows - Firefox',
            'Windows - Edge',
            'macOS - Safari',
            'macOS - Chrome',
            'iPhone - Safari',
            'Android - Chrome',
            'iPad - Safari',
            'Linux - Firefox',
        ];

        $ubicaciones = [
            'Bogotá, Colombia',
            'Medellín, Colombia',
            'Cali, Colombia',
            'Barranquilla, Colombia',
            'Cartagena, Colombia',
            'Bucaramanga, Colombia',
            'Pereira, Colombia',
            'Manizales, Colombia',
            'Santa Marta, Colombia',
            'Ibagué, Colombia',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
        ];

        return [
            'usuario_id' => Usuario::factory(),
            'token_id' => Str::random(40),
            'ip' => fake('es_ES')->ipv4(),
            'user_agent' => fake('es_ES')->randomElement($userAgents),
            'dispositivo' => fake('es_ES')->randomElement($dispositivos),
            'ubicacion' => fake('es_ES')->randomElement($ubicaciones),
            'ultimo_acceso' => fake('es_ES')->dateTimeBetween('-7 days', 'now'),
            'expira_en' => fake('es_ES')->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'ultimo_acceso' => fake('es_ES')->dateTimeBetween('-1 hour', 'now'),
            'expira_en' => fake('es_ES')->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    public function expirada(): static
    {
        return $this->state(fn (array $attributes) => [
            'ultimo_acceso' => fake('es_ES')->dateTimeBetween('-30 days', '-7 days'),
            'expira_en' => fake('es_ES')->dateTimeBetween('-7 days', '-1 day'),
        ]);
    }

    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'ultimo_acceso' => now(),
        ]);
    }

    public function windows(): static
    {
        return $this->state(fn (array $attributes) => [
            'dispositivo' => fake('es_ES')->randomElement(['Windows - Chrome', 'Windows - Firefox', 'Windows - Edge']),
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ]);
    }

    public function mac(): static
    {
        return $this->state(fn (array $attributes) => [
            'dispositivo' => fake('es_ES')->randomElement(['macOS - Safari', 'macOS - Chrome']),
            'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
        ]);
    }

    public function movil(): static
    {
        return $this->state(fn (array $attributes) => [
            'dispositivo' => fake('es_ES')->randomElement(['iPhone - Safari', 'Android - Chrome', 'iPad - Safari']),
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        ]);
    }

    public function enBogota(): static
    {
        return $this->state(fn (array $attributes) => [
            'ubicacion' => 'Bogotá, Colombia',
        ]);
    }

    public function enMedellin(): static
    {
        return $this->state(fn (array $attributes) => [
            'ubicacion' => 'Medellín, Colombia',
        ]);
    }
}

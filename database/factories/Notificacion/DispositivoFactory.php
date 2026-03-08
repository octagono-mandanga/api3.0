<?php

namespace Database\Factories\Notificacion;

use App\Models\Notificacion\Dispositivo;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DispositivoFactory extends Factory
{
    protected $model = Dispositivo::class;

    public function definition(): array
    {
        $plataformas = ['android', 'ios', 'web'];
        $estados = ['activo', 'inactivo', 'revocado'];

        $dispositivos = [
            'android' => [
                'nombres' => ['Samsung Galaxy', 'Xiaomi Redmi', 'Motorola Moto', 'Huawei', 'OnePlus', 'Google Pixel', 'Realme'],
                'modelos' => ['S23 Ultra', 'Note 12 Pro', 'G84', 'P50', '11T', 'Pixel 8', 'GT Neo 5'],
            ],
            'ios' => [
                'nombres' => ['iPhone', 'iPad', 'iPad Pro', 'iPad Air', 'iPad Mini'],
                'modelos' => ['15 Pro Max', '14 Plus', 'SE 2023', 'Pro 12.9', 'Air 5th Gen', 'Mini 6th Gen'],
            ],
            'web' => [
                'nombres' => ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'],
                'modelos' => ['Windows 11', 'macOS Sonoma', 'Ubuntu 24.04', 'Windows 10', 'macOS Ventura'],
            ],
        ];

        $plataforma = fake('es_ES')->randomElement($plataformas);
        $config = $dispositivos[$plataforma];

        return [
            'usuario_id' => Usuario::factory(),
            'token' => Str::random(64),
            'plataforma' => $plataforma,
            'nombre_dispositivo' => fake('es_ES')->randomElement($config['nombres']),
            'modelo' => fake('es_ES')->randomElement($config['modelos']),
            'version_app' => fake('es_ES')->semver(),
            'ultimo_uso' => fake('es_ES')->dateTimeBetween('-3 months', 'now'),
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'plataforma' => 'android',
            'nombre_dispositivo' => fake('es_ES')->randomElement(['Samsung Galaxy', 'Xiaomi Redmi', 'Motorola Moto', 'Huawei']),
            'modelo' => fake('es_ES')->randomElement(['S23 Ultra', 'Note 12 Pro', 'G84', 'P50']),
        ]);
    }

    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'plataforma' => 'ios',
            'nombre_dispositivo' => fake('es_ES')->randomElement(['iPhone', 'iPad', 'iPad Pro']),
            'modelo' => fake('es_ES')->randomElement(['15 Pro Max', '14 Plus', 'Pro 12.9']),
        ]);
    }

    public function web(): static
    {
        return $this->state(fn (array $attributes) => [
            'plataforma' => 'web',
            'nombre_dispositivo' => fake('es_ES')->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
            'modelo' => fake('es_ES')->randomElement(['Windows 11', 'macOS Sonoma', 'Ubuntu 24.04']),
        ]);
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activo',
            'ultimo_uso' => fake('es_ES')->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
            'ultimo_uso' => fake('es_ES')->dateTimeBetween('-6 months', '-1 month'),
        ]);
    }

    public function revocado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'revocado',
        ]);
    }

    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'ultimo_uso' => fake('es_ES')->dateTimeBetween('-1 day', 'now'),
        ]);
    }
}

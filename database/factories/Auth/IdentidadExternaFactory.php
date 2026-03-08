<?php

namespace Database\Factories\Auth;

use App\Models\Auth\IdentidadExterna;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdentidadExternaFactory extends Factory
{
    protected $model = IdentidadExterna::class;

    public function definition(): array
    {
        $proveedores = ['google', 'microsoft', 'facebook', 'apple'];

        return [
            'usuario_id' => Usuario::factory(),
            'proveedor' => fake()->randomElement($proveedores),
            'proveedor_id' => fake()->uuid(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => fake()->optional(0.5)->imageUrl(200, 200, 'people'),
            'datos' => [
                'nombre' => fake('es_ES')->name(),
                'locale' => 'es_CO',
            ],
        ];
    }

    public function google(): static
    {
        return $this->state(fn(array $attributes) => [
            'proveedor' => 'google',
        ]);
    }

    public function microsoft(): static
    {
        return $this->state(fn(array $attributes) => [
            'proveedor' => 'microsoft',
        ]);
    }
}

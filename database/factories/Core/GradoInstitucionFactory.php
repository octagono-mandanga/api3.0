<?php

namespace Database\Factories\Core;

use App\Models\Core\GradoInstitucion;
use App\Models\Core\NivelInstitucion;
use App\Models\Core\Grado;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradoInstitucionFactory extends Factory
{
    protected $model = GradoInstitucion::class;

    public function definition(): array
    {
        return [
            'nivel_institucion_id' => NivelInstitucion::factory(),
            'grado_id' => Grado::factory(),
            'nombre_personalizado' => fake()->optional(0.3)->randomElement([
                'Grado Primero A', 'Grado Segundo B', 'Grado Tercero C'
            ]),
            'cupo_maximo' => fake()->numberBetween(25, 45),
            'edad_minima' => fake()->optional(0.5)->numberBetween(3, 16),
            'edad_maxima' => fake()->optional(0.5)->numberBetween(6, 20),
            'estado' => true,
        ];
    }

    public function sinCupo(): static
    {
        return $this->state(fn(array $attributes) => [
            'cupo_maximo' => null,
        ]);
    }

    public function conEdades(): static
    {
        return $this->state(fn(array $attributes) => [
            'edad_minima' => 5,
            'edad_maxima' => 7,
        ]);
    }
}

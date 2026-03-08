<?php

namespace Database\Factories\Core;

use App\Models\Core\NivelInstitucion;
use App\Models\Core\Institucion;
use App\Models\Core\NivelEducativo;
use App\Models\Core\ModeloEducativo;
use Illuminate\Database\Eloquent\Factories\Factory;

class NivelInstitucionFactory extends Factory
{
    protected $model = NivelInstitucion::class;

    public function definition(): array
    {
        return [
            'institucion_id' => Institucion::factory(),
            'nivel_id' => NivelEducativo::factory(),
            'modelo_educativo_id' => ModeloEducativo::factory(),
            'configuracion' => [
                'periodos_academicos' => fake()->randomElement([2, 3, 4]),
                'escala_calificacion' => fake()->randomElement(['1-5', '1-10', '0-100']),
            ],
            'estado' => true,
        ];
    }

    public function sinModelo(): static
    {
        return $this->state(fn(array $attributes) => [
            'modelo_educativo_id' => null,
        ]);
    }
}

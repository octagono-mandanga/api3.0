<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\EscalaCalificacion;
use App\Models\Core\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscalaCalificacionFactory extends Factory
{
    protected $model = EscalaCalificacion::class;

    public function definition(): array
    {
        $escalas = [
            ['nombre' => 'Escala 1-5', 'min' => 1.0, 'max' => 5.0, 'aprobatorio' => 3.0, 'decimales' => 1],
            ['nombre' => 'Escala 1-10', 'min' => 1.0, 'max' => 10.0, 'aprobatorio' => 6.0, 'decimales' => 1],
            ['nombre' => 'Escala 0-100', 'min' => 0.0, 'max' => 100.0, 'aprobatorio' => 60.0, 'decimales' => 0],
            ['nombre' => 'Escala Cualitativa', 'min' => 1.0, 'max' => 4.0, 'aprobatorio' => 2.0, 'decimales' => 0],
        ];

        $escala = fake()->randomElement($escalas);

        return [
            'institucion_id' => Institucion::factory(),
            'nombre' => $escala['nombre'],
            'valor_minimo' => $escala['min'],
            'valor_maximo' => $escala['max'],
            'valor_aprobatorio' => $escala['aprobatorio'],
            'decimales' => $escala['decimales'],
            'es_predeterminada' => false,
            'estado' => true,
        ];
    }

    public function escala15(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Escala 1-5',
            'valor_minimo' => 1.0,
            'valor_maximo' => 5.0,
            'valor_aprobatorio' => 3.0,
            'decimales' => 1,
        ]);
    }

    public function escala100(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Escala 0-100',
            'valor_minimo' => 0.0,
            'valor_maximo' => 100.0,
            'valor_aprobatorio' => 60.0,
            'decimales' => 0,
        ]);
    }

    public function predeterminada(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_predeterminada' => true,
        ]);
    }
}

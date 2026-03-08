<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\NotaPeriodo;
use App\Models\Inscripcion\Matricula;
use App\Models\Inscripcion\DocenteAsignatura;
use App\Models\Evaluacion\Periodo;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaPeriodoFactory extends Factory
{
    protected $model = NotaPeriodo::class;

    public function definition(): array
    {
        $nota = fake()->randomFloat(1, 1.0, 5.0);

        return [
            'matricula_id' => Matricula::factory(),
            'docente_asignatura_id' => DocenteAsignatura::factory(),
            'periodo_id' => Periodo::factory(),
            'nota_calculada' => $nota,
            'nota_ajustada' => fake()->optional(0.1)->randomFloat(1, 1.0, 5.0),
            'total_actividades' => fake()->numberBetween(3, 10),
            'actividades_calificadas' => fake()->numberBetween(3, 10),
            'inasistencias' => fake()->numberBetween(0, 5),
            'observacion' => fake()->optional(0.4, fn() => fake('es_ES')->sentence(10)),
            'fecha_cierre' => fake()->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'estado' => 'abierta',
        ];
    }

    public function aprobada(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => fake()->randomFloat(1, 3.0, 5.0),
        ]);
    }

    public function reprobada(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => fake()->randomFloat(1, 1.0, 2.9),
        ]);
    }

    public function cerrada(): static
    {
        return $this->state(fn(array $attributes) => [
            'fecha_cierre' => now(),
            'estado' => 'cerrada',
        ]);
    }

    public function conAjuste(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => 2.8,
            'nota_ajustada' => 3.0,
            'observacion' => 'Nota ajustada por recuperación.',
        ]);
    }
}

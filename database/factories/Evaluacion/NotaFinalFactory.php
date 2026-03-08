<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\NotaFinal;
use App\Models\Inscripcion\Matricula;
use App\Models\Inscripcion\DocenteAsignatura;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotaFinalFactory extends Factory
{
    protected $model = NotaFinal::class;

    public function definition(): array
    {
        $nota = fake()->randomFloat(1, 1.0, 5.0);

        return [
            'matricula_id' => Matricula::factory(),
            'docente_asignatura_id' => DocenteAsignatura::factory(),
            'nota_calculada' => $nota,
            'nota_ajustada' => fake()->optional(0.1)->randomFloat(1, 1.0, 5.0),
            'nota_habilitacion' => fake()->optional(0.05)->randomFloat(1, 1.0, 5.0),
            'nota_definitiva' => $nota,
            'aprobada' => $nota >= 3.0,
            'periodos_perdidos' => $nota < 3.0 ? fake()->numberBetween(1, 4) : 0,
            'total_inasistencias' => fake()->numberBetween(0, 20),
            'porcentaje_inasistencia' => fake()->randomFloat(2, 0, 15),
            'observacion' => fake()->optional(0.3, fn() => fake('es_ES')->sentence(10)),
            'estado' => 'definitiva',
        ];
    }

    public function aprobada(): static
    {
        $nota = fake()->randomFloat(1, 3.0, 5.0);
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => $nota,
            'nota_definitiva' => $nota,
            'aprobada' => true,
            'periodos_perdidos' => 0,
        ]);
    }

    public function reprobada(): static
    {
        $nota = fake()->randomFloat(1, 1.0, 2.9);
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => $nota,
            'nota_definitiva' => $nota,
            'aprobada' => false,
            'periodos_perdidos' => fake()->numberBetween(2, 4),
        ]);
    }

    public function habilitada(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => 2.5,
            'nota_habilitacion' => 3.5,
            'nota_definitiva' => 3.0,
            'aprobada' => true,
            'observacion' => 'Aprobó por habilitación.',
        ]);
    }

    public function porInasistencia(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota_calculada' => 4.0,
            'nota_definitiva' => 1.0,
            'aprobada' => false,
            'porcentaje_inasistencia' => 28.5,
            'observacion' => 'Reprobado por exceder el 25% de inasistencias.',
        ]);
    }

    public function provisional(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'provisional',
        ]);
    }
}

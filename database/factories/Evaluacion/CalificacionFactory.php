<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\Calificacion;
use App\Models\Evaluacion\Actividad;
use App\Models\Inscripcion\Matricula;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalificacionFactory extends Factory
{
    protected $model = Calificacion::class;

    public function definition(): array
    {
        $nota = fake()->randomFloat(1, 1.0, 5.0);

        return [
            'actividad_id' => Actividad::factory(),
            'matricula_id' => Matricula::factory(),
            'nota' => $nota,
            'observacion' => fake()->optional(0.3, fn() => fake('es_ES')->sentence(8)),
            'fecha_calificacion' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d H:i:s'),
            'calificado_por' => Usuario::factory()->docente(),
            'entrega_tardia' => fake()->boolean(10),
            'estado' => 'publicada',
        ];
    }

    public function aprobada(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota' => fake()->randomFloat(1, 3.0, 5.0),
        ]);
    }

    public function reprobada(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota' => fake()->randomFloat(1, 1.0, 2.9),
        ]);
    }

    public function excelente(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota' => fake()->randomFloat(1, 4.5, 5.0),
            'observacion' => 'Excelente trabajo. Felicitaciones.',
        ]);
    }

    public function sinNota(): static
    {
        return $this->state(fn(array $attributes) => [
            'nota' => null,
            'estado' => 'pendiente',
        ]);
    }

    public function tardia(): static
    {
        return $this->state(fn(array $attributes) => [
            'entrega_tardia' => true,
            'observacion' => 'Entrega tardía.',
        ]);
    }
}

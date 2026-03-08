<?php

namespace Database\Factories\Inscripcion;

use App\Models\Inscripcion\Matricula;
use App\Models\Inscripcion\Estudiante;
use App\Models\Inscripcion\Curso;
use App\Models\Core\Lectivo;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatriculaFactory extends Factory
{
    protected $model = Matricula::class;

    public function definition(): array
    {
        $tiposMatricula = ['nuevo', 'antiguo', 'transferencia', 'reintegro'];

        return [
            'estudiante_id' => Estudiante::factory(),
            'curso_id' => Curso::factory(),
            'lectivo_id' => Lectivo::factory(),
            'numero_matricula' => fake()->unique()->numerify('MAT-####-####'),
            'fecha_matricula' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'tipo_matricula' => fake()->randomElement($tiposMatricula),
            'procedencia' => fake()->optional(0.3)->company() . ' (Colegio)',
            'observaciones' => fake()->optional(0.2, fn() => fake('es_ES')->sentence(8)),
            'estado' => 'activa',
        ];
    }

    public function nuevo(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo_matricula' => 'nuevo',
        ]);
    }

    public function antiguo(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo_matricula' => 'antiguo',
            'procedencia' => null,
        ]);
    }

    public function transferencia(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo_matricula' => 'transferencia',
            'procedencia' => fake()->company() . ' (Colegio)',
        ]);
    }

    public function retirada(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'retirada',
        ]);
    }

    public function suspendida(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'suspendida',
        ]);
    }

    public function graduada(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'graduada',
        ]);
    }
}

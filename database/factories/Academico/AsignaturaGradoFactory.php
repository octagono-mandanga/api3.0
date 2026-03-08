<?php

namespace Database\Factories\Academico;

use App\Models\Academico\AsignaturaGrado;
use App\Models\Academico\Asignatura;
use App\Models\Core\GradoInstitucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsignaturaGradoFactory extends Factory
{
    protected $model = AsignaturaGrado::class;

    public function definition(): array
    {
        return [
            'asignatura_id' => Asignatura::factory(),
            'grado_institucion_id' => GradoInstitucion::factory(),
            'intensidad_horaria' => fake()->numberBetween(1, 6),
            'es_obligatoria' => fake()->boolean(80),
            'estado' => true,
        ];
    }

    public function obligatoria(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_obligatoria' => true,
        ]);
    }

    public function optativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_obligatoria' => false,
        ]);
    }
}

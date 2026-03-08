<?php

namespace Database\Factories\Inscripcion;

use App\Models\Inscripcion\DocenteAsignatura;
use App\Models\Core\Institucion;
use App\Models\Inscripcion\Curso;
use App\Models\Academico\AsignaturaGrado;
use App\Models\Auth\Usuario;
use App\Models\Core\Lectivo;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocenteAsignaturaFactory extends Factory
{
    protected $model = DocenteAsignatura::class;

    public function definition(): array
    {
        return [
            'institucion_id' => Institucion::factory(),
            'curso_id' => Curso::factory(),
            'asignatura_grado_id' => AsignaturaGrado::factory(),
            'docente_id' => Usuario::factory()->docente(),
            'lectivo_id' => Lectivo::factory(),
            'es_titular' => fake()->boolean(70),
            'fecha_asignacion' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'fecha_finalizacion' => null,
            'estado' => 'activo',
        ];
    }

    public function titular(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_titular' => true,
        ]);
    }

    public function suplente(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_titular' => false,
        ]);
    }

    public function finalizado(): static
    {
        return $this->state(fn(array $attributes) => [
            'fecha_finalizacion' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'estado' => 'inactivo',
        ]);
    }
}

<?php

namespace Database\Factories\Inscripcion;

use App\Models\Inscripcion\Curso;
use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Core\GradoInstitucion;
use App\Models\Core\Jornada;
use App\Models\Core\Lectivo;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class CursoFactory extends Factory
{
    protected $model = Curso::class;

    public function definition(): array
    {
        $grupos = ['A', 'B', 'C', 'D', '01', '02', '03'];

        return [
            'institucion_id' => Institucion::factory(),
            'sede_id' => Sede::factory(),
            'grado_institucion_id' => GradoInstitucion::factory(),
            'jornada_id' => Jornada::factory(),
            'lectivo_id' => Lectivo::factory(),
            'nombre' => fake()->randomElement($grupos),
            'director_grupo_id' => Usuario::factory()->docente(),
            'aula' => fake()->optional(0.8)->randomElement([
                'Salón 101', 'Salón 102', 'Salón 201', 'Salón 202',
                'Aula Múltiple', 'Laboratorio 1', 'Sala de Sistemas'
            ]),
            'cupo_maximo' => fake()->numberBetween(25, 40),
            'estado' => 'activo',
        ];
    }

    public function sinDirector(): static
    {
        return $this->state(fn(array $attributes) => [
            'director_grupo_id' => null,
        ]);
    }

    public function lleno(): static
    {
        return $this->state(fn(array $attributes) => [
            'cupo_maximo' => 35,
        ]);
    }

    public function cerrado(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'cerrado',
        ]);
    }
}

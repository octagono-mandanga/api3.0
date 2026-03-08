<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\TipoActividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoActividadFactory extends Factory
{
    protected $model = TipoActividad::class;

    public function definition(): array
    {
        $tipos = [
            ['nombre' => 'Taller', 'codigo' => 'TAL', 'porcentaje' => 20],
            ['nombre' => 'Quiz', 'codigo' => 'QUI', 'porcentaje' => 15],
            ['nombre' => 'Evaluación Escrita', 'codigo' => 'EVA', 'porcentaje' => 30],
            ['nombre' => 'Exposición', 'codigo' => 'EXP', 'porcentaje' => 15],
            ['nombre' => 'Trabajo en Clase', 'codigo' => 'TRC', 'porcentaje' => 10],
            ['nombre' => 'Tarea', 'codigo' => 'TAR', 'porcentaje' => 10],
            ['nombre' => 'Proyecto', 'codigo' => 'PRO', 'porcentaje' => 25],
            ['nombre' => 'Participación', 'codigo' => 'PAR', 'porcentaje' => 10],
            ['nombre' => 'Laboratorio', 'codigo' => 'LAB', 'porcentaje' => 20],
            ['nombre' => 'Autoevaluación', 'codigo' => 'AUT', 'porcentaje' => 5],
            ['nombre' => 'Coevaluación', 'codigo' => 'COE', 'porcentaje' => 5],
        ];

        $tipo = fake()->randomElement($tipos);

        return [
            'nombre' => $tipo['nombre'],
            'codigo' => $tipo['codigo'],
            'descripcion' => "Actividad de tipo {$tipo['nombre']}",
            'porcentaje_sugerido' => $tipo['porcentaje'],
            'icono' => fake()->optional(0.5)->randomElement(['assignment', 'quiz', 'school', 'groups', 'science']),
            'estado' => true,
        ];
    }

    public function evaluacion(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Evaluación Escrita',
            'codigo' => 'EVA',
            'porcentaje_sugerido' => 30,
        ]);
    }

    public function taller(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Taller',
            'codigo' => 'TAL',
            'porcentaje_sugerido' => 20,
        ]);
    }

    public function quiz(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Quiz',
            'codigo' => 'QUI',
            'porcentaje_sugerido' => 15,
        ]);
    }
}

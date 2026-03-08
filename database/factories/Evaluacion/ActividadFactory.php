<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\Actividad;
use App\Models\Core\Institucion;
use App\Models\Inscripcion\DocenteAsignatura;
use App\Models\Academico\Logro;
use App\Models\Evaluacion\Periodo;
use App\Models\Evaluacion\TipoActividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition(): array
    {
        $titulos = [
            'Evaluación de conceptos básicos',
            'Taller práctico en clase',
            'Quiz de repaso',
            'Exposición grupal',
            'Proyecto de investigación',
            'Trabajo colaborativo',
            'Práctica de laboratorio',
            'Ejercicios de aplicación',
            'Comprensión de lectura',
            'Resolución de problemas',
        ];

        return [
            'institucion_id' => Institucion::factory(),
            'docente_asignatura_id' => DocenteAsignatura::factory(),
            'logro_id' => Logro::factory(),
            'periodo_id' => Periodo::factory(),
            'tipo_id' => TipoActividad::factory(),
            'titulo' => fake()->randomElement($titulos),
            'descripcion' => fake('es_ES')->paragraph(2),
            'instrucciones' => fake('es_ES')->optional(0.6)->paragraph(3),
            'fecha_asignacion' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'fecha_entrega' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'porcentaje' => fake()->randomElement([10, 15, 20, 25, 30]),
            'valor_maximo' => 5.0,
            'permite_entrega_tardia' => fake()->boolean(30),
            'es_grupal' => fake()->boolean(20),
            'visible_estudiantes' => true,
            'estado' => 'activa',
        ];
    }

    public function evaluacion(): static
    {
        return $this->state(fn(array $attributes) => [
            'titulo' => 'Evaluación escrita',
            'porcentaje' => 30,
            'es_grupal' => false,
            'permite_entrega_tardia' => false,
        ]);
    }

    public function taller(): static
    {
        return $this->state(fn(array $attributes) => [
            'titulo' => 'Taller práctico',
            'porcentaje' => 20,
            'permite_entrega_tardia' => true,
        ]);
    }

    public function proyecto(): static
    {
        return $this->state(fn(array $attributes) => [
            'titulo' => 'Proyecto de período',
            'porcentaje' => 25,
            'es_grupal' => true,
            'permite_entrega_tardia' => true,
        ]);
    }

    public function cerrada(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'cerrada',
        ]);
    }

    public function borrador(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'borrador',
            'visible_estudiantes' => false,
        ]);
    }
}

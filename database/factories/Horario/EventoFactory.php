<?php

namespace Database\Factories\Horario;

use App\Models\Horario\Evento;
use App\Models\Horario\TipoEvento;
use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Inscripcion\Curso;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventoFactory extends Factory
{
    protected $model = Evento::class;

    public function definition(): array
    {
        $titulos = [
            'Reunión de padres de familia',
            'Evaluación final de período',
            'Salida pedagógica al museo',
            'Día del idioma',
            'Jornada de integración',
            'Entrega de boletines académicos',
            'Izada de bandera mensual',
            'Festival de la ciencia',
            'Día de la familia',
            'Olimpiadas matemáticas',
            'Feria empresarial',
            'Ceremonia de graduación',
            'Día del estudiante',
            'Semana cultural',
            'Jornada de vacunación',
        ];

        $descripciones = [
            'Evento importante para toda la comunidad educativa.',
            'Se requiere la asistencia de todos los estudiantes del curso.',
            'Los padres de familia están cordialmente invitados.',
            'Actividad programada según el calendario académico.',
            'Favor confirmar asistencia con anticipación.',
        ];

        $ubicaciones = [
            'Auditorio principal',
            'Cancha deportiva',
            'Salón de eventos',
            'Biblioteca',
            'Patio central',
            'Aula múltiple',
            'Teatro escolar',
            'Laboratorio de ciencias',
        ];

        $publicosObjetivo = ['todos', 'estudiantes', 'docentes', 'padres', 'administrativos'];
        $estados = ['programado', 'en_curso', 'finalizado', 'cancelado', 'pospuesto'];

        $fechaInicio = fake('es_ES')->dateTimeBetween('-1 month', '+3 months');

        return [
            'institucion_id' => Institucion::factory(),
            'sede_id' => Sede::factory(),
            'tipo_id' => TipoEvento::factory(),
            'titulo' => fake('es_ES')->randomElement($titulos),
            'descripcion' => fake('es_ES')->randomElement($descripciones),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => fake('es_ES')->dateTimeBetween($fechaInicio, (clone $fechaInicio)->modify('+8 hours')),
            'todo_el_dia' => fake('es_ES')->boolean(30),
            'ubicacion' => fake('es_ES')->randomElement($ubicaciones),
            'publico_objetivo' => fake('es_ES')->randomElement($publicosObjetivo),
            'curso_id' => fake('es_ES')->boolean(40) ? Curso::factory() : null,
            'creador_id' => Usuario::factory(),
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function todoElDia(): static
    {
        return $this->state(function (array $attributes) {
            $fecha = fake('es_ES')->dateTimeBetween('-1 month', '+3 months');
            return [
                'todo_el_dia' => true,
                'fecha_inicio' => $fecha->setTime(0, 0),
                'fecha_fin' => (clone $fecha)->setTime(23, 59),
            ];
        });
    }

    public function conHorario(): static
    {
        return $this->state(fn (array $attributes) => [
            'todo_el_dia' => false,
        ]);
    }

    public function programado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'programado',
            'fecha_inicio' => fake('es_ES')->dateTimeBetween('+1 day', '+3 months'),
        ]);
    }

    public function enCurso(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'en_curso',
            'fecha_inicio' => now()->subHours(2),
            'fecha_fin' => now()->addHours(2),
        ]);
    }

    public function finalizado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'finalizado',
            'fecha_inicio' => fake('es_ES')->dateTimeBetween('-3 months', '-1 day'),
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'cancelado',
        ]);
    }

    public function pospuesto(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'pospuesto',
        ]);
    }

    public function paraTodos(): static
    {
        return $this->state(fn (array $attributes) => [
            'publico_objetivo' => 'todos',
            'curso_id' => null,
        ]);
    }

    public function paraCurso(): static
    {
        return $this->state(fn (array $attributes) => [
            'publico_objetivo' => 'estudiantes',
            'curso_id' => Curso::factory(),
        ]);
    }

    public function paraDocentes(): static
    {
        return $this->state(fn (array $attributes) => [
            'publico_objetivo' => 'docentes',
            'curso_id' => null,
        ]);
    }

    public function paraPadres(): static
    {
        return $this->state(fn (array $attributes) => [
            'publico_objetivo' => 'padres',
        ]);
    }
}

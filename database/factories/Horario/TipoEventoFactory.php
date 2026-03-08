<?php

namespace Database\Factories\Horario;

use App\Models\Horario\TipoEvento;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoEventoFactory extends Factory
{
    protected $model = TipoEvento::class;

    public function definition(): array
    {
        $tipos = [
            ['nombre' => 'Reunión de padres', 'color' => '#2196F3', 'icono' => 'people', 'requiere_asistencia' => true],
            ['nombre' => 'Evaluación', 'color' => '#F44336', 'icono' => 'assignment', 'requiere_asistencia' => true],
            ['nombre' => 'Salida pedagógica', 'color' => '#4CAF50', 'icono' => 'directions_bus', 'requiere_asistencia' => true],
            ['nombre' => 'Día festivo', 'color' => '#FF9800', 'icono' => 'celebration', 'requiere_asistencia' => false],
            ['nombre' => 'Jornada pedagógica', 'color' => '#9C27B0', 'icono' => 'school', 'requiere_asistencia' => false],
            ['nombre' => 'Entrega de boletines', 'color' => '#00BCD4', 'icono' => 'description', 'requiere_asistencia' => true],
            ['nombre' => 'Acto cívico', 'color' => '#795548', 'icono' => 'flag', 'requiere_asistencia' => true],
            ['nombre' => 'Izada de bandera', 'color' => '#FFEB3B', 'icono' => 'outlined_flag', 'requiere_asistencia' => true],
            ['nombre' => 'Evento deportivo', 'color' => '#8BC34A', 'icono' => 'sports_soccer', 'requiere_asistencia' => false],
            ['nombre' => 'Evento cultural', 'color' => '#E91E63', 'icono' => 'theater_comedy', 'requiere_asistencia' => false],
            ['nombre' => 'Vacaciones', 'color' => '#607D8B', 'icono' => 'beach_access', 'requiere_asistencia' => false],
            ['nombre' => 'Capacitación docente', 'color' => '#3F51B5', 'icono' => 'model_training', 'requiere_asistencia' => false],
        ];

        $tipo = fake('es_ES')->randomElement($tipos);
        $estados = ['activo', 'inactivo'];

        return [
            'nombre' => $tipo['nombre'],
            'color' => $tipo['color'],
            'icono' => $tipo['icono'],
            'requiere_asistencia' => $tipo['requiere_asistencia'],
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function conAsistencia(): static
    {
        return $this->state(fn (array $attributes) => [
            'requiere_asistencia' => true,
        ]);
    }

    public function sinAsistencia(): static
    {
        return $this->state(fn (array $attributes) => [
            'requiere_asistencia' => false,
        ]);
    }

    public function academico(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => fake('es_ES')->randomElement(['Evaluación', 'Entrega de boletines', 'Jornada pedagógica']),
            'color' => '#F44336',
            'icono' => 'school',
        ]);
    }

    public function institucional(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => fake('es_ES')->randomElement(['Acto cívico', 'Izada de bandera', 'Reunión de padres']),
            'color' => '#2196F3',
            'icono' => 'flag',
        ]);
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activo',
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}

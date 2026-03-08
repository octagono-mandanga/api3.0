<?php

namespace Database\Factories\Observador;

use App\Models\Observador\TipoObservacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoObservacionFactory extends Factory
{
    protected $model = TipoObservacion::class;

    public function definition(): array
    {
        $tipos = [
            ['nombre' => 'Felicitación', 'valoracion' => 'positiva', 'color' => '#4CAF50', 'icono' => 'thumb_up'],
            ['nombre' => 'Reconocimiento académico', 'valoracion' => 'positiva', 'color' => '#2196F3', 'icono' => 'school'],
            ['nombre' => 'Buen comportamiento', 'valoracion' => 'positiva', 'color' => '#8BC34A', 'icono' => 'emoji_events'],
            ['nombre' => 'Llamado de atención', 'valoracion' => 'negativa', 'color' => '#FF9800', 'icono' => 'warning'],
            ['nombre' => 'Falta disciplinaria', 'valoracion' => 'negativa', 'color' => '#F44336', 'icono' => 'gavel'],
            ['nombre' => 'Falta grave', 'valoracion' => 'negativa', 'color' => '#B71C1C', 'icono' => 'report'],
            ['nombre' => 'Observación general', 'valoracion' => 'neutra', 'color' => '#9E9E9E', 'icono' => 'note'],
            ['nombre' => 'Compromiso académico', 'valoracion' => 'neutra', 'color' => '#607D8B', 'icono' => 'assignment'],
        ];

        $tipo = fake()->randomElement($tipos);

        return [
            'nombre' => $tipo['nombre'],
            'valoracion' => $tipo['valoracion'],
            'color' => $tipo['color'],
            'icono' => $tipo['icono'],
            'estado' => true,
        ];
    }

    public function positiva(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Felicitación',
            'valoracion' => 'positiva',
            'color' => '#4CAF50',
        ]);
    }

    public function negativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Falta disciplinaria',
            'valoracion' => 'negativa',
            'color' => '#F44336',
        ]);
    }

    public function neutra(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Observación general',
            'valoracion' => 'neutra',
            'color' => '#9E9E9E',
        ]);
    }
}

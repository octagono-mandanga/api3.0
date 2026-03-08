<?php

namespace Database\Factories\Core;

use App\Models\Core\Jornada;
use Illuminate\Database\Eloquent\Factories\Factory;

class JornadaFactory extends Factory
{
    protected $model = Jornada::class;

    public function definition(): array
    {
        $jornadas = [
            ['nombre' => 'Mañana', 'codigo' => 'MAN', 'hora_inicio' => '06:30:00', 'hora_fin' => '12:30:00'],
            ['nombre' => 'Tarde', 'codigo' => 'TAR', 'hora_inicio' => '12:30:00', 'hora_fin' => '18:30:00'],
            ['nombre' => 'Noche', 'codigo' => 'NOC', 'hora_inicio' => '18:00:00', 'hora_fin' => '22:00:00'],
            ['nombre' => 'Única', 'codigo' => 'UNI', 'hora_inicio' => '07:00:00', 'hora_fin' => '15:00:00'],
            ['nombre' => 'Sabatina', 'codigo' => 'SAB', 'hora_inicio' => '07:00:00', 'hora_fin' => '13:00:00'],
            ['nombre' => 'Dominical', 'codigo' => 'DOM', 'hora_inicio' => '07:00:00', 'hora_fin' => '13:00:00'],
        ];

        $jornada = fake()->randomElement($jornadas);

        return [
            'nombre' => $jornada['nombre'],
            'codigo' => $jornada['codigo'],
            'hora_inicio' => $jornada['hora_inicio'],
            'hora_fin' => $jornada['hora_fin'],
            'estado' => true,
        ];
    }

    public function manana(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Mañana',
            'codigo' => 'MAN',
            'hora_inicio' => '06:30:00',
            'hora_fin' => '12:30:00',
        ]);
    }

    public function tarde(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Tarde',
            'codigo' => 'TAR',
            'hora_inicio' => '12:30:00',
            'hora_fin' => '18:30:00',
        ]);
    }

    public function unica(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Única',
            'codigo' => 'UNI',
            'hora_inicio' => '07:00:00',
            'hora_fin' => '15:00:00',
        ]);
    }
}

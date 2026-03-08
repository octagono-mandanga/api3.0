<?php

namespace Database\Factories\Horario;

use App\Models\Horario\FranjaHoraria;
use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Core\Jornada;
use Illuminate\Database\Eloquent\Factories\Factory;

class FranjaHorariaFactory extends Factory
{
    protected $model = FranjaHoraria::class;

    public function definition(): array
    {
        $tipos = ['clase', 'descanso', 'almuerzo', 'formacion'];
        $estados = ['activo', 'inactivo'];

        $franjas = [
            ['nombre' => 'Primera hora', 'hora_inicio' => '06:30:00', 'hora_fin' => '07:25:00', 'tipo' => 'clase', 'orden' => 1],
            ['nombre' => 'Segunda hora', 'hora_inicio' => '07:25:00', 'hora_fin' => '08:20:00', 'tipo' => 'clase', 'orden' => 2],
            ['nombre' => 'Descanso', 'hora_inicio' => '08:20:00', 'hora_fin' => '08:50:00', 'tipo' => 'descanso', 'orden' => 3],
            ['nombre' => 'Tercera hora', 'hora_inicio' => '08:50:00', 'hora_fin' => '09:45:00', 'tipo' => 'clase', 'orden' => 4],
            ['nombre' => 'Cuarta hora', 'hora_inicio' => '09:45:00', 'hora_fin' => '10:40:00', 'tipo' => 'clase', 'orden' => 5],
            ['nombre' => 'Quinta hora', 'hora_inicio' => '10:40:00', 'hora_fin' => '11:35:00', 'tipo' => 'clase', 'orden' => 6],
            ['nombre' => 'Almuerzo', 'hora_inicio' => '11:35:00', 'hora_fin' => '12:30:00', 'tipo' => 'almuerzo', 'orden' => 7],
            ['nombre' => 'Sexta hora', 'hora_inicio' => '12:30:00', 'hora_fin' => '13:25:00', 'tipo' => 'clase', 'orden' => 8],
        ];

        $franja = fake('es_ES')->randomElement($franjas);

        return [
            'institucion_id' => Institucion::factory(),
            'sede_id' => Sede::factory(),
            'jornada_id' => Jornada::factory(),
            'nombre' => $franja['nombre'],
            'hora_inicio' => $franja['hora_inicio'],
            'hora_fin' => $franja['hora_fin'],
            'tipo' => $franja['tipo'],
            'orden' => $franja['orden'],
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function clase(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'clase',
        ]);
    }

    public function descanso(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'Descanso',
            'tipo' => 'descanso',
        ]);
    }

    public function almuerzo(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'Almuerzo',
            'tipo' => 'almuerzo',
        ]);
    }

    public function formacion(): static
    {
        return $this->state(fn (array $attributes) => [
            'nombre' => 'Formación',
            'tipo' => 'formacion',
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

    public function manana(): static
    {
        return $this->state(fn (array $attributes) => [
            'hora_inicio' => fake('es_ES')->randomElement(['06:30:00', '07:00:00', '07:30:00']),
            'hora_fin' => fake('es_ES')->randomElement(['12:00:00', '12:30:00', '13:00:00']),
        ]);
    }

    public function tarde(): static
    {
        return $this->state(fn (array $attributes) => [
            'hora_inicio' => fake('es_ES')->randomElement(['13:00:00', '13:30:00', '14:00:00']),
            'hora_fin' => fake('es_ES')->randomElement(['18:00:00', '18:30:00', '19:00:00']),
        ]);
    }
}

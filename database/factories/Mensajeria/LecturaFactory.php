<?php

namespace Database\Factories\Mensajeria;

use App\Models\Mensajeria\Lectura;
use App\Models\Mensajeria\Mensaje;
use App\Models\Mensajeria\Participante;
use Illuminate\Database\Eloquent\Factories\Factory;

class LecturaFactory extends Factory
{
    protected $model = Lectura::class;

    public function definition(): array
    {
        return [
            'mensaje_id' => Mensaje::factory(),
            'participante_id' => Participante::factory(),
            'leido_en' => fake('es_ES')->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function reciente(): static
    {
        return $this->state(fn (array $attributes) => [
            'leido_en' => fake('es_ES')->dateTimeBetween('-1 hour', 'now'),
        ]);
    }

    public function antigua(): static
    {
        return $this->state(fn (array $attributes) => [
            'leido_en' => fake('es_ES')->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }

    public function ayer(): static
    {
        return $this->state(fn (array $attributes) => [
            'leido_en' => fake('es_ES')->dateTimeBetween('-1 day', '-1 day'),
        ]);
    }

    public function hoy(): static
    {
        return $this->state(fn (array $attributes) => [
            'leido_en' => now(),
        ]);
    }
}

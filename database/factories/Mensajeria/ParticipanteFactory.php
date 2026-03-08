<?php

namespace Database\Factories\Mensajeria;

use App\Models\Mensajeria\Participante;
use App\Models\Mensajeria\Conversacion;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipanteFactory extends Factory
{
    protected $model = Participante::class;

    public function definition(): array
    {
        $roles = ['propietario', 'administrador', 'miembro', 'invitado'];

        return [
            'conversacion_id' => Conversacion::factory(),
            'usuario_id' => Usuario::factory(),
            'rol' => fake('es_ES')->randomElement($roles),
            'silenciado' => fake('es_ES')->boolean(10),
            'fecha_union' => fake('es_ES')->dateTimeBetween('-1 year', 'now'),
            'fecha_salida' => null,
        ];
    }

    public function propietario(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'propietario',
        ]);
    }

    public function administrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'administrador',
        ]);
    }

    public function miembro(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'miembro',
        ]);
    }

    public function invitado(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'invitado',
        ]);
    }

    public function silenciado(): static
    {
        return $this->state(fn (array $attributes) => [
            'silenciado' => true,
        ]);
    }

    public function retirado(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_salida' => fake('es_ES')->dateTimeBetween($attributes['fecha_union'] ?? '-6 months', 'now'),
        ]);
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'silenciado' => false,
            'fecha_salida' => null,
        ]);
    }
}

<?php

namespace Database\Factories\Inscripcion;

use App\Models\Inscripcion\Acudiente;
use App\Models\Inscripcion\Estudiante;
use App\Models\Inscripcion\TipoParentesco;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcudienteFactory extends Factory
{
    protected $model = Acudiente::class;

    public function definition(): array
    {
        return [
            'estudiante_id' => Estudiante::factory(),
            'usuario_id' => Usuario::factory(),
            'tipo_parentesco_id' => TipoParentesco::factory(),
            'es_principal' => true,
            'es_autorizado_recoger' => fake()->boolean(80),
            'recibe_notificaciones' => true,
            'ocupacion' => fake()->optional(0.7)->randomElement([
                'Empleado', 'Independiente', 'Comerciante', 'Profesional',
                'Ama de casa', 'Pensionado', 'Desempleado', 'Estudiante'
            ]),
            'lugar_trabajo' => fake()->optional(0.5)->company(),
            'telefono_trabajo' => fake()->optional(0.4)->numerify('#######'),
            'estado' => 'activo',
        ];
    }

    public function principal(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_principal' => true,
            'recibe_notificaciones' => true,
            'es_autorizado_recoger' => true,
        ]);
    }

    public function secundario(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_principal' => false,
        ]);
    }

    public function sinAutorizacion(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_autorizado_recoger' => false,
        ]);
    }
}

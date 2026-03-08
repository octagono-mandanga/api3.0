<?php

namespace Database\Factories\Core;

use App\Models\Core\Perfil;
use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Core\RolInstitucion;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PerfilFactory extends Factory
{
    protected $model = Perfil::class;

    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'institucion_id' => Institucion::factory(),
            'sede_id' => null,
            'rol_institucion_id' => RolInstitucion::factory(),
            'cargo' => fake()->optional(0.5)->randomElement([
                'Docente de Área', 'Coordinador Académico', 'Orientador',
                'Director de Grupo', 'Secretaria', 'Auxiliar Administrativo'
            ]),
            'fecha_vinculacion' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'fecha_desvinculacion' => null,
            'es_principal' => true,
            'estado' => 'activo',
        ];
    }

    public function conSede(): static
    {
        return $this->state(fn(array $attributes) => [
            'sede_id' => Sede::factory(),
        ]);
    }

    public function desvinculado(): static
    {
        return $this->state(fn(array $attributes) => [
            'fecha_desvinculacion' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'estado' => 'inactivo',
            'es_principal' => false,
        ]);
    }

    public function secundario(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_principal' => false,
        ]);
    }
}

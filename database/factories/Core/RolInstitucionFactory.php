<?php

namespace Database\Factories\Core;

use App\Models\Core\RolInstitucion;
use App\Models\Core\Institucion;
use App\Models\Auth\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolInstitucionFactory extends Factory
{
    protected $model = RolInstitucion::class;

    public function definition(): array
    {
        return [
            'institucion_id' => Institucion::factory(),
            'rol_id' => Rol::factory(),
            'permisos' => [
                'ver_estudiantes' => true,
                'editar_estudiantes' => fake()->boolean(50),
                'ver_calificaciones' => true,
                'editar_calificaciones' => fake()->boolean(30),
                'ver_observador' => true,
                'editar_observador' => fake()->boolean(40),
                'ver_reportes' => true,
                'generar_reportes' => fake()->boolean(50),
            ],
            'configuracion' => [],
            'estado' => true,
        ];
    }

    public function administrador(): static
    {
        return $this->state(fn(array $attributes) => [
            'permisos' => [
                'ver_estudiantes' => true,
                'editar_estudiantes' => true,
                'ver_calificaciones' => true,
                'editar_calificaciones' => true,
                'ver_observador' => true,
                'editar_observador' => true,
                'ver_reportes' => true,
                'generar_reportes' => true,
                'configurar_sistema' => true,
            ],
        ]);
    }

    public function docente(): static
    {
        return $this->state(fn(array $attributes) => [
            'permisos' => [
                'ver_estudiantes' => true,
                'editar_estudiantes' => false,
                'ver_calificaciones' => true,
                'editar_calificaciones' => true,
                'ver_observador' => true,
                'editar_observador' => true,
                'ver_reportes' => true,
                'generar_reportes' => false,
            ],
        ]);
    }

    public function acudiente(): static
    {
        return $this->state(fn(array $attributes) => [
            'permisos' => [
                'ver_estudiantes' => true,
                'editar_estudiantes' => false,
                'ver_calificaciones' => true,
                'editar_calificaciones' => false,
                'ver_observador' => true,
                'editar_observador' => false,
                'ver_reportes' => false,
                'generar_reportes' => false,
            ],
        ]);
    }
}

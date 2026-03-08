<?php

namespace Database\Factories\Core;

use App\Models\Core\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        $planes = [
            ['nombre' => 'Básico', 'codigo' => 'basico', 'max_estudiantes' => 100, 'max_docentes' => 10, 'max_sedes' => 1, 'precio' => 0],
            ['nombre' => 'Estándar', 'codigo' => 'estandar', 'max_estudiantes' => 500, 'max_docentes' => 50, 'max_sedes' => 3, 'precio' => 99000],
            ['nombre' => 'Premium', 'codigo' => 'premium', 'max_estudiantes' => 2000, 'max_docentes' => 200, 'max_sedes' => 10, 'precio' => 299000],
            ['nombre' => 'Enterprise', 'codigo' => 'enterprise', 'max_estudiantes' => null, 'max_docentes' => null, 'max_sedes' => null, 'precio' => 599000],
        ];

        $plan = fake()->randomElement($planes);

        return [
            'nombre' => $plan['nombre'],
            'codigo' => $plan['codigo'],
            'descripcion' => "Plan {$plan['nombre']} para instituciones educativas",
            'max_estudiantes' => $plan['max_estudiantes'],
            'max_docentes' => $plan['max_docentes'],
            'max_sedes' => $plan['max_sedes'],
            'precio_mensual' => $plan['precio'],
            'caracteristicas' => [
                'modulo_academico' => true,
                'modulo_observador' => $plan['codigo'] !== 'basico',
                'modulo_mensajeria' => $plan['codigo'] !== 'basico',
                'modulo_horarios' => in_array($plan['codigo'], ['premium', 'enterprise']),
                'soporte_prioritario' => $plan['codigo'] === 'enterprise',
            ],
            'estado' => true,
        ];
    }

    public function basico(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Básico',
            'codigo' => 'basico',
            'max_estudiantes' => 100,
            'max_docentes' => 10,
            'max_sedes' => 1,
            'precio_mensual' => 0,
        ]);
    }

    public function premium(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Premium',
            'codigo' => 'premium',
            'max_estudiantes' => 2000,
            'max_docentes' => 200,
            'max_sedes' => 10,
            'precio_mensual' => 299000,
        ]);
    }
}

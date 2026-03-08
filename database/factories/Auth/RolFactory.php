<?php

namespace Database\Factories\Auth;

use App\Models\Auth\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;

class RolFactory extends Factory
{
    protected $model = Rol::class;

    public function definition(): array
    {
        $roles = [
            ['nombre' => 'Super Administrador', 'codigo' => 'super_admin', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Administrador', 'codigo' => 'admin', 'descripcion' => 'Administrador de institución'],
            ['nombre' => 'Rector', 'codigo' => 'rector', 'descripcion' => 'Rector de la institución'],
            ['nombre' => 'Coordinador', 'codigo' => 'coordinador', 'descripcion' => 'Coordinador académico'],
            ['nombre' => 'Docente', 'codigo' => 'docente', 'descripcion' => 'Docente de asignaturas'],
            ['nombre' => 'Director de Grupo', 'codigo' => 'director_grupo', 'descripcion' => 'Director de grupo asignado'],
            ['nombre' => 'Orientador', 'codigo' => 'orientador', 'descripcion' => 'Orientador escolar'],
            ['nombre' => 'Secretaria', 'codigo' => 'secretaria', 'descripcion' => 'Secretaria académica'],
            ['nombre' => 'Acudiente', 'codigo' => 'acudiente', 'descripcion' => 'Padre/madre/acudiente'],
            ['nombre' => 'Estudiante', 'codigo' => 'estudiante', 'descripcion' => 'Estudiante matriculado'],
        ];

        $rol = fake()->randomElement($roles);

        return [
            'nombre' => $rol['nombre'],
            'codigo' => $rol['codigo'],
            'descripcion' => $rol['descripcion'],
            'es_sistema' => false,
            'estado' => true,
        ];
    }

    public function sistema(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_sistema' => true,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Super Administrador',
            'codigo' => 'super_admin',
            'descripcion' => 'Acceso total al sistema',
            'es_sistema' => true,
        ]);
    }

    public function docente(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Docente',
            'codigo' => 'docente',
            'descripcion' => 'Docente de asignaturas',
        ]);
    }

    public function estudiante(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Estudiante',
            'codigo' => 'estudiante',
            'descripcion' => 'Estudiante matriculado',
        ]);
    }

    public function acudiente(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Acudiente',
            'codigo' => 'acudiente',
            'descripcion' => 'Padre/madre/acudiente',
        ]);
    }
}

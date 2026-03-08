<?php

namespace Database\Factories\Academico;

use App\Models\Academico\Asignatura;
use App\Models\Academico\AreaFormacion;
use App\Models\Core\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsignaturaFactory extends Factory
{
    protected $model = Asignatura::class;

    public function definition(): array
    {
        $asignaturas = [
            ['nombre' => 'Matemáticas', 'codigo' => 'MAT'],
            ['nombre' => 'Lengua Castellana', 'codigo' => 'LEN'],
            ['nombre' => 'Inglés', 'codigo' => 'ING'],
            ['nombre' => 'Ciencias Naturales', 'codigo' => 'NAT'],
            ['nombre' => 'Ciencias Sociales', 'codigo' => 'SOC'],
            ['nombre' => 'Educación Física', 'codigo' => 'EFI'],
            ['nombre' => 'Educación Artística', 'codigo' => 'ART'],
            ['nombre' => 'Tecnología e Informática', 'codigo' => 'TEC'],
            ['nombre' => 'Ética y Valores', 'codigo' => 'ETI'],
            ['nombre' => 'Religión', 'codigo' => 'REL'],
            ['nombre' => 'Física', 'codigo' => 'FIS'],
            ['nombre' => 'Química', 'codigo' => 'QUI'],
            ['nombre' => 'Biología', 'codigo' => 'BIO'],
            ['nombre' => 'Filosofía', 'codigo' => 'FIL'],
            ['nombre' => 'Economía', 'codigo' => 'ECO'],
            ['nombre' => 'Cátedra de Paz', 'codigo' => 'PAZ'],
        ];

        $asignatura = fake()->randomElement($asignaturas);

        return [
            'institucion_id' => Institucion::factory(),
            'area_id' => AreaFormacion::factory(),
            'nombre' => $asignatura['nombre'],
            'codigo' => $asignatura['codigo'],
            'descripcion' => "Asignatura de {$asignatura['nombre']}",
            'intensidad_horaria' => fake()->numberBetween(1, 6),
            'es_obligatoria' => fake()->boolean(80),
            'estado' => true,
        ];
    }

    public function matematicas(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Matemáticas',
            'codigo' => 'MAT',
            'descripcion' => 'Asignatura de Matemáticas',
            'intensidad_horaria' => 5,
            'es_obligatoria' => true,
        ]);
    }

    public function lenguaCastellana(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Lengua Castellana',
            'codigo' => 'LEN',
            'descripcion' => 'Asignatura de Lengua Castellana',
            'intensidad_horaria' => 5,
            'es_obligatoria' => true,
        ]);
    }

    public function ingles(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Inglés',
            'codigo' => 'ING',
            'descripcion' => 'Asignatura de Inglés',
            'intensidad_horaria' => 3,
            'es_obligatoria' => true,
        ]);
    }

    public function optativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'es_obligatoria' => false,
        ]);
    }
}

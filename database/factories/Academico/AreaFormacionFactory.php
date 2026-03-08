<?php

namespace Database\Factories\Academico;

use App\Models\Academico\AreaFormacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class AreaFormacionFactory extends Factory
{
    protected $model = AreaFormacion::class;

    public function definition(): array
    {
        $areas = [
            ['nombre' => 'Ciencias Naturales y Educación Ambiental', 'codigo' => 'CNAT'],
            ['nombre' => 'Ciencias Sociales, Historia, Geografía y Constitución Política', 'codigo' => 'CSOC'],
            ['nombre' => 'Educación Artística y Cultural', 'codigo' => 'EART'],
            ['nombre' => 'Educación Ética y Valores Humanos', 'codigo' => 'EETI'],
            ['nombre' => 'Educación Física, Recreación y Deportes', 'codigo' => 'EFIS'],
            ['nombre' => 'Educación Religiosa', 'codigo' => 'EREL'],
            ['nombre' => 'Humanidades, Lengua Castellana e Idiomas Extranjeros', 'codigo' => 'HUMA'],
            ['nombre' => 'Matemáticas', 'codigo' => 'MATE'],
            ['nombre' => 'Tecnología e Informática', 'codigo' => 'TECN'],
            ['nombre' => 'Filosofía', 'codigo' => 'FILO'],
            ['nombre' => 'Ciencias Económicas y Políticas', 'codigo' => 'CECO'],
        ];

        $area = fake()->randomElement($areas);

        return [
            'nombre' => $area['nombre'],
            'codigo' => $area['codigo'],
            'descripcion' => "Área de formación en {$area['nombre']}",
            'estado' => true,
        ];
    }

    public function matematicas(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Matemáticas',
            'codigo' => 'MATE',
            'descripcion' => 'Área de formación en Matemáticas',
        ]);
    }

    public function lenguaje(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Humanidades, Lengua Castellana e Idiomas Extranjeros',
            'codigo' => 'HUMA',
            'descripcion' => 'Área de formación en Humanidades',
        ]);
    }

    public function cienciasNaturales(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Ciencias Naturales y Educación Ambiental',
            'codigo' => 'CNAT',
            'descripcion' => 'Área de formación en Ciencias Naturales',
        ]);
    }
}

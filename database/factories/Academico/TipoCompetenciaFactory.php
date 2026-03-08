<?php

namespace Database\Factories\Academico;

use App\Models\Academico\TipoCompetencia;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoCompetenciaFactory extends Factory
{
    protected $model = TipoCompetencia::class;

    public function definition(): array
    {
        $tipos = [
            ['nombre' => 'Interpretativa', 'codigo' => 'INT', 'descripcion' => 'Comprensión e interpretación de información'],
            ['nombre' => 'Argumentativa', 'codigo' => 'ARG', 'descripcion' => 'Capacidad de dar razones y explicaciones'],
            ['nombre' => 'Propositiva', 'codigo' => 'PRO', 'descripcion' => 'Capacidad de generar hipótesis y propuestas'],
            ['nombre' => 'Comunicativa', 'codigo' => 'COM', 'descripcion' => 'Habilidades de comunicación efectiva'],
            ['nombre' => 'Ciudadana', 'codigo' => 'CIU', 'descripcion' => 'Competencias para la convivencia'],
            ['nombre' => 'Laboral', 'codigo' => 'LAB', 'descripcion' => 'Competencias para el trabajo'],
            ['nombre' => 'Científica', 'codigo' => 'CIE', 'descripcion' => 'Pensamiento científico e investigativo'],
        ];

        $tipo = fake()->randomElement($tipos);

        return [
            'nombre' => $tipo['nombre'],
            'codigo' => $tipo['codigo'],
            'descripcion' => $tipo['descripcion'],
            'estado' => true,
        ];
    }

    public function interpretativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Interpretativa',
            'codigo' => 'INT',
            'descripcion' => 'Comprensión e interpretación de información',
        ]);
    }

    public function argumentativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Argumentativa',
            'codigo' => 'ARG',
            'descripcion' => 'Capacidad de dar razones y explicaciones',
        ]);
    }

    public function propositiva(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Propositiva',
            'codigo' => 'PRO',
            'descripcion' => 'Capacidad de generar hipótesis y propuestas',
        ]);
    }
}

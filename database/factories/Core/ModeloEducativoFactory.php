<?php

namespace Database\Factories\Core;

use App\Models\Core\ModeloEducativo;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModeloEducativoFactory extends Factory
{
    protected $model = ModeloEducativo::class;

    public function definition(): array
    {
        $modelos = [
            ['nombre' => 'Tradicional', 'codigo' => 'TRAD', 'descripcion' => 'Modelo educativo tradicional presencial'],
            ['nombre' => 'Escuela Nueva', 'codigo' => 'ENUE', 'descripcion' => 'Metodología activa multigrado'],
            ['nombre' => 'Aceleración del Aprendizaje', 'codigo' => 'ACEL', 'descripcion' => 'Modelo flexible para extra-edad'],
            ['nombre' => 'Postprimaria', 'codigo' => 'POST', 'descripcion' => 'Secundaria en zonas rurales'],
            ['nombre' => 'Telesecundaria', 'codigo' => 'TELE', 'descripcion' => 'Educación a distancia rural'],
            ['nombre' => 'SAT', 'codigo' => 'SAT', 'descripcion' => 'Sistema de Aprendizaje Tutorial'],
            ['nombre' => 'CAFAM', 'codigo' => 'CAFA', 'descripcion' => 'Educación continuada CAFAM'],
            ['nombre' => 'Media Rural', 'codigo' => 'MERU', 'descripcion' => 'Media académica rural'],
        ];

        $modelo = fake()->randomElement($modelos);

        return [
            'nombre' => $modelo['nombre'],
            'codigo' => $modelo['codigo'],
            'descripcion' => $modelo['descripcion'],
            'estado' => true,
        ];
    }

    public function tradicional(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Tradicional',
            'codigo' => 'TRAD',
            'descripcion' => 'Modelo educativo tradicional presencial',
        ]);
    }

    public function escuelaNueva(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Escuela Nueva',
            'codigo' => 'ENUE',
            'descripcion' => 'Metodología activa multigrado',
        ]);
    }
}

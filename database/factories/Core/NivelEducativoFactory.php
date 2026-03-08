<?php

namespace Database\Factories\Core;

use App\Models\Core\NivelEducativo;
use Illuminate\Database\Eloquent\Factories\Factory;

class NivelEducativoFactory extends Factory
{
    protected $model = NivelEducativo::class;

    public function definition(): array
    {
        $niveles = [
            ['nombre' => 'Preescolar', 'codigo' => 'PRE', 'orden' => 1],
            ['nombre' => 'Básica Primaria', 'codigo' => 'PRI', 'orden' => 2],
            ['nombre' => 'Básica Secundaria', 'codigo' => 'SEC', 'orden' => 3],
            ['nombre' => 'Media', 'codigo' => 'MED', 'orden' => 4],
            ['nombre' => 'Técnica', 'codigo' => 'TEC', 'orden' => 5],
        ];

        $nivel = fake()->randomElement($niveles);

        return [
            'nombre' => $nivel['nombre'],
            'codigo' => $nivel['codigo'],
            'orden' => $nivel['orden'],
            'estado' => true,
        ];
    }

    public function preescolar(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Preescolar',
            'codigo' => 'PRE',
            'orden' => 1,
        ]);
    }

    public function primaria(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Básica Primaria',
            'codigo' => 'PRI',
            'orden' => 2,
        ]);
    }

    public function secundaria(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Básica Secundaria',
            'codigo' => 'SEC',
            'orden' => 3,
        ]);
    }

    public function media(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Media',
            'codigo' => 'MED',
            'orden' => 4,
        ]);
    }
}

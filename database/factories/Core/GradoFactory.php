<?php

namespace Database\Factories\Core;

use App\Models\Core\Grado;
use App\Models\Core\NivelEducativo;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradoFactory extends Factory
{
    protected $model = Grado::class;

    public function definition(): array
    {
        $grados = [
            ['nombre' => 'Pre-jardín', 'codigo' => 'PJ', 'orden' => 1],
            ['nombre' => 'Jardín', 'codigo' => 'JA', 'orden' => 2],
            ['nombre' => 'Transición', 'codigo' => 'TR', 'orden' => 3],
            ['nombre' => 'Primero', 'codigo' => '01', 'orden' => 4],
            ['nombre' => 'Segundo', 'codigo' => '02', 'orden' => 5],
            ['nombre' => 'Tercero', 'codigo' => '03', 'orden' => 6],
            ['nombre' => 'Cuarto', 'codigo' => '04', 'orden' => 7],
            ['nombre' => 'Quinto', 'codigo' => '05', 'orden' => 8],
            ['nombre' => 'Sexto', 'codigo' => '06', 'orden' => 9],
            ['nombre' => 'Séptimo', 'codigo' => '07', 'orden' => 10],
            ['nombre' => 'Octavo', 'codigo' => '08', 'orden' => 11],
            ['nombre' => 'Noveno', 'codigo' => '09', 'orden' => 12],
            ['nombre' => 'Décimo', 'codigo' => '10', 'orden' => 13],
            ['nombre' => 'Undécimo', 'codigo' => '11', 'orden' => 14],
        ];

        $grado = fake()->randomElement($grados);

        return [
            'nivel_id' => NivelEducativo::factory(),
            'nombre' => $grado['nombre'],
            'codigo' => $grado['codigo'],
            'orden' => $grado['orden'],
            'estado' => true,
        ];
    }

    public function transicion(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Transición',
            'codigo' => 'TR',
            'orden' => 3,
        ]);
    }

    public function primero(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Primero',
            'codigo' => '01',
            'orden' => 4,
        ]);
    }

    public function sexto(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Sexto',
            'codigo' => '06',
            'orden' => 9,
        ]);
    }

    public function undecimo(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Undécimo',
            'codigo' => '11',
            'orden' => 14,
        ]);
    }
}

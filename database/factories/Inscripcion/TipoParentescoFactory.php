<?php

namespace Database\Factories\Inscripcion;

use App\Models\Inscripcion\TipoParentesco;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoParentescoFactory extends Factory
{
    protected $model = TipoParentesco::class;

    public function definition(): array
    {
        $parentescos = [
            ['nombre' => 'Madre', 'codigo' => 'MAD'],
            ['nombre' => 'Padre', 'codigo' => 'PAD'],
            ['nombre' => 'Abuelo/a', 'codigo' => 'ABU'],
            ['nombre' => 'Tío/a', 'codigo' => 'TIO'],
            ['nombre' => 'Hermano/a', 'codigo' => 'HER'],
            ['nombre' => 'Tutor Legal', 'codigo' => 'TUT'],
            ['nombre' => 'Padrastro/Madrastra', 'codigo' => 'PAS'],
            ['nombre' => 'Otro Familiar', 'codigo' => 'OTR'],
        ];

        $parentesco = fake()->randomElement($parentescos);

        return [
            'nombre' => $parentesco['nombre'],
            'codigo' => $parentesco['codigo'],
            'estado' => true,
        ];
    }

    public function madre(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Madre',
            'codigo' => 'MAD',
        ]);
    }

    public function padre(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Padre',
            'codigo' => 'PAD',
        ]);
    }

    public function tutorLegal(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Tutor Legal',
            'codigo' => 'TUT',
        ]);
    }
}

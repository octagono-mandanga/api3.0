<?php

namespace Database\Factories\Ref;

use App\Models\Ref\Etnia;
use Illuminate\Database\Eloquent\Factories\Factory;

class EtniaFactory extends Factory
{
    protected $model = Etnia::class;

    public function definition(): array
    {
        $etnias = [
            'Ninguna',
            'Afrocolombiano',
            'Raizal',
            'Palenquero',
            'Indígena',
            'Rom (Gitano)',
        ];

        return [
            'nombre' => fake()->unique()->randomElement($etnias),
            'codigo' => fake()->unique()->lexify('???'),
            'estado' => true,
        ];
    }
}

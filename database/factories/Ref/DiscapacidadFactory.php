<?php

namespace Database\Factories\Ref;

use App\Models\Ref\Discapacidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiscapacidadFactory extends Factory
{
    protected $model = Discapacidad::class;

    public function definition(): array
    {
        $discapacidades = [
            'Ninguna',
            'Física',
            'Auditiva',
            'Visual',
            'Cognitiva',
            'Psicosocial',
            'Múltiple',
            'Sordoceguera',
        ];

        return [
            'nombre' => fake()->unique()->randomElement($discapacidades),
            'codigo' => fake()->unique()->lexify('???'),
            'estado' => true,
        ];
    }
}

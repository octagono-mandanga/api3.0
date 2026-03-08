<?php

namespace Database\Factories\Ref;

use App\Models\Ref\TipoDocumento;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoDocumentoFactory extends Factory
{
    protected $model = TipoDocumento::class;

    public function definition(): array
    {
        $tipos = [
            ['nombre' => 'Cédula de Ciudadanía', 'codigo' => 'CC'],
            ['nombre' => 'Tarjeta de Identidad', 'codigo' => 'TI'],
            ['nombre' => 'Cédula de Extranjería', 'codigo' => 'CE'],
            ['nombre' => 'Pasaporte', 'codigo' => 'PA'],
            ['nombre' => 'Registro Civil', 'codigo' => 'RC'],
            ['nombre' => 'NIT', 'codigo' => 'NIT'],
            ['nombre' => 'NUIP', 'codigo' => 'NUIP'],
        ];

        $tipo = fake()->randomElement($tipos);

        return [
            'nombre' => $tipo['nombre'],
            'codigo' => $tipo['codigo'],
            'estado' => true,
        ];
    }

    public function inactivo(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => false,
        ]);
    }
}

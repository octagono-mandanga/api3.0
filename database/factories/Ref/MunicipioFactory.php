<?php

namespace Database\Factories\Ref;

use App\Models\Ref\Municipio;
use App\Models\Ref\Departamento;
use Illuminate\Database\Eloquent\Factories\Factory;

class MunicipioFactory extends Factory
{
    protected $model = Municipio::class;

    public function definition(): array
    {
        $municipios = [
            'Medellín', 'Bogotá', 'Cali', 'Barranquilla', 'Cartagena',
            'Bucaramanga', 'Pereira', 'Manizales', 'Santa Marta', 'Ibagué',
            'Cúcuta', 'Villavicencio', 'Pasto', 'Montería', 'Neiva',
            'Armenia', 'Popayán', 'Sincelejo', 'Valledupar', 'Tunja',
        ];

        return [
            'departamento_id' => Departamento::factory(),
            'nombre' => fake()->randomElement($municipios),
            'codigo' => fake()->unique()->numerify('####'),
            'estado' => true,
        ];
    }
}

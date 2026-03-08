<?php

namespace Database\Factories\Core;

use App\Models\Core\Sede;
use App\Models\Core\Institucion;
use App\Models\Ref\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

class SedeFactory extends Factory
{
    protected $model = Sede::class;

    public function definition(): array
    {
        $nombres = [
            'Sede Principal', 'Sede Centro', 'Sede Norte', 'Sede Sur',
            'Sede Oriental', 'Sede Occidental', 'Anexo 1', 'Anexo 2',
            'Sede Primaria', 'Sede Secundaria', 'Sede Preescolar'
        ];

        return [
            'institucion_id' => Institucion::factory(),
            'nombre' => fake()->randomElement($nombres),
            'codigo' => fake()->unique()->numerify('SEDE###'),
            'direccion' => fake('es_ES')->streetAddress(),
            'municipio_id' => Municipio::factory(),
            'telefono' => fake()->optional(0.7)->numerify('#######'),
            'email' => fake()->optional(0.5)->companyEmail(),
            'coordinador_nombre' => fake()->optional(0.6, fn() => fake('es_ES')->name()),
            'es_principal' => false,
            'estado' => 'activo',
        ];
    }

    public function principal(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Sede Principal',
            'es_principal' => true,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}

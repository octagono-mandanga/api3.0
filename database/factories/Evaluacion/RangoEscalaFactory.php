<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\RangoEscala;
use App\Models\Evaluacion\EscalaCalificacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class RangoEscalaFactory extends Factory
{
    protected $model = RangoEscala::class;

    public function definition(): array
    {
        $rangos = [
            ['nombre' => 'Bajo', 'abreviatura' => 'BJ', 'descripcion' => 'Desempeño bajo', 'min' => 1.0, 'max' => 2.9, 'color' => '#F44336', 'aprueba' => false],
            ['nombre' => 'Básico', 'abreviatura' => 'BS', 'descripcion' => 'Desempeño básico', 'min' => 3.0, 'max' => 3.9, 'color' => '#FF9800', 'aprueba' => true],
            ['nombre' => 'Alto', 'abreviatura' => 'AL', 'descripcion' => 'Desempeño alto', 'min' => 4.0, 'max' => 4.5, 'color' => '#8BC34A', 'aprueba' => true],
            ['nombre' => 'Superior', 'abreviatura' => 'SU', 'descripcion' => 'Desempeño superior', 'min' => 4.6, 'max' => 5.0, 'color' => '#4CAF50', 'aprueba' => true],
        ];

        $rango = fake()->randomElement($rangos);

        return [
            'escala_id' => EscalaCalificacion::factory(),
            'nombre' => $rango['nombre'],
            'abreviatura' => $rango['abreviatura'],
            'descripcion' => $rango['descripcion'],
            'valor_minimo' => $rango['min'],
            'valor_maximo' => $rango['max'],
            'color' => $rango['color'],
            'aprueba' => $rango['aprueba'],
            'orden' => fake()->numberBetween(1, 4),
        ];
    }

    public function bajo(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Bajo',
            'abreviatura' => 'BJ',
            'valor_minimo' => 1.0,
            'valor_maximo' => 2.9,
            'color' => '#F44336',
            'aprueba' => false,
            'orden' => 1,
        ]);
    }

    public function basico(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Básico',
            'abreviatura' => 'BS',
            'valor_minimo' => 3.0,
            'valor_maximo' => 3.9,
            'color' => '#FF9800',
            'aprueba' => true,
            'orden' => 2,
        ]);
    }

    public function alto(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Alto',
            'abreviatura' => 'AL',
            'valor_minimo' => 4.0,
            'valor_maximo' => 4.5,
            'color' => '#8BC34A',
            'aprueba' => true,
            'orden' => 3,
        ]);
    }

    public function superior(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Superior',
            'abreviatura' => 'SU',
            'valor_minimo' => 4.6,
            'valor_maximo' => 5.0,
            'color' => '#4CAF50',
            'aprueba' => true,
            'orden' => 4,
        ]);
    }
}

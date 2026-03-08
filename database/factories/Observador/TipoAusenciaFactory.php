<?php

namespace Database\Factories\Observador;

use App\Models\Observador\TipoAusencia;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoAusenciaFactory extends Factory
{
    protected $model = TipoAusencia::class;

    public function definition(): array
    {
        $tipos = [
            ['nombre' => 'Enfermedad', 'codigo' => 'ENF', 'justificable' => true],
            ['nombre' => 'Cita médica', 'codigo' => 'CIT', 'justificable' => true],
            ['nombre' => 'Calamidad doméstica', 'codigo' => 'CAL', 'justificable' => true],
            ['nombre' => 'Representación institucional', 'codigo' => 'REP', 'justificable' => true],
            ['nombre' => 'Trámite personal', 'codigo' => 'TRA', 'justificable' => true],
            ['nombre' => 'Sin justificación', 'codigo' => 'SIN', 'justificable' => false],
            ['nombre' => 'Evasión de clase', 'codigo' => 'EVA', 'justificable' => false],
            ['nombre' => 'Suspensión', 'codigo' => 'SUS', 'justificable' => false],
        ];

        $tipo = fake()->randomElement($tipos);

        return [
            'nombre' => $tipo['nombre'],
            'codigo' => $tipo['codigo'],
            'justificable' => $tipo['justificable'],
            'estado' => true,
        ];
    }

    public function enfermedad(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Enfermedad',
            'codigo' => 'ENF',
            'justificable' => true,
        ]);
    }

    public function sinJustificar(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Sin justificación',
            'codigo' => 'SIN',
            'justificable' => false,
        ]);
    }

    public function evasion(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Evasión de clase',
            'codigo' => 'EVA',
            'justificable' => false,
        ]);
    }
}

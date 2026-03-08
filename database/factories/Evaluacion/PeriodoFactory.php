<?php

namespace Database\Factories\Evaluacion;

use App\Models\Evaluacion\Periodo;
use App\Models\Core\Institucion;
use App\Models\Core\Lectivo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodoFactory extends Factory
{
    protected $model = Periodo::class;

    public function definition(): array
    {
        $numero = fake()->numberBetween(1, 4);
        $anio = fake()->numberBetween(2020, 2030);

        return [
            'institucion_id' => Institucion::factory(),
            'lectivo_id' => Lectivo::factory(),
            'nombre' => "Período {$numero}",
            'numero' => $numero,
            'fecha_inicio' => "{$anio}-0{$numero}-01",
            'fecha_fin' => "{$anio}-0" . ($numero + 2) . "-15",
            'fecha_cierre_notas' => "{$anio}-0" . ($numero + 2) . "-20",
            'porcentaje' => 25.00,
            'es_final' => $numero === 4,
            'estado' => 'activo',
        ];
    }

    public function primero(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Período 1',
            'numero' => 1,
            'porcentaje' => 25.00,
            'es_final' => false,
        ]);
    }

    public function segundo(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Período 2',
            'numero' => 2,
            'porcentaje' => 25.00,
            'es_final' => false,
        ]);
    }

    public function tercero(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Período 3',
            'numero' => 3,
            'porcentaje' => 25.00,
            'es_final' => false,
        ]);
    }

    public function cuarto(): static
    {
        return $this->state(fn(array $attributes) => [
            'nombre' => 'Período 4',
            'numero' => 4,
            'porcentaje' => 25.00,
            'es_final' => true,
        ]);
    }

    public function cerrado(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'cerrado',
        ]);
    }
}

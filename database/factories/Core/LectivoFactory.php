<?php

namespace Database\Factories\Core;

use App\Models\Core\Lectivo;
use App\Models\Core\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LectivoFactory extends Factory
{
    protected $model = Lectivo::class;

    public function definition(): array
    {
        $anio = fake()->numberBetween(2020, 2030);

        return [
            'institucion_id' => Institucion::factory(),
            'nombre' => "Año Lectivo {$anio}",
            'anio' => $anio,
            'fecha_inicio' => "{$anio}-01-15",
            'fecha_fin' => "{$anio}-11-30",
            'fecha_inicio_clases' => "{$anio}-01-20",
            'fecha_fin_clases' => "{$anio}-11-15",
            'semanas_lectivas' => fake()->numberBetween(38, 42),
            'es_actual' => false,
            'estado' => 'activo',
        ];
    }

    public function actual(): static
    {
        $anioActual = (int) date('Y');
        return $this->state(fn(array $attributes) => [
            'nombre' => "Año Lectivo {$anioActual}",
            'anio' => $anioActual,
            'fecha_inicio' => "{$anioActual}-01-15",
            'fecha_fin' => "{$anioActual}-11-30",
            'fecha_inicio_clases' => "{$anioActual}-01-20",
            'fecha_fin_clases' => "{$anioActual}-11-15",
            'es_actual' => true,
        ]);
    }

    public function finalizado(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'finalizado',
            'es_actual' => false,
        ]);
    }

    public function planificacion(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'planificacion',
            'es_actual' => false,
        ]);
    }
}

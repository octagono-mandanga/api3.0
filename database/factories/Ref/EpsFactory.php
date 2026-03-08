<?php

namespace Database\Factories\Ref;

use App\Models\Ref\Eps;
use Illuminate\Database\Eloquent\Factories\Factory;

class EpsFactory extends Factory
{
    protected $model = Eps::class;

    public function definition(): array
    {
        $epsList = [
            ['nombre' => 'Nueva EPS', 'codigo' => 'EPS001'],
            ['nombre' => 'Sanitas', 'codigo' => 'EPS002'],
            ['nombre' => 'Sura', 'codigo' => 'EPS003'],
            ['nombre' => 'Salud Total', 'codigo' => 'EPS004'],
            ['nombre' => 'Coomeva', 'codigo' => 'EPS005'],
            ['nombre' => 'Compensar', 'codigo' => 'EPS006'],
            ['nombre' => 'Famisanar', 'codigo' => 'EPS007'],
            ['nombre' => 'Coosalud', 'codigo' => 'EPS008'],
            ['nombre' => 'Mutual Ser', 'codigo' => 'EPS009'],
            ['nombre' => 'Aliansalud', 'codigo' => 'EPS010'],
            ['nombre' => 'Capital Salud', 'codigo' => 'EPS011'],
            ['nombre' => 'Savia Salud', 'codigo' => 'EPS012'],
        ];

        $eps = fake()->randomElement($epsList);

        return [
            'nombre' => $eps['nombre'],
            'codigo' => $eps['codigo'],
            'estado' => true,
        ];
    }
}

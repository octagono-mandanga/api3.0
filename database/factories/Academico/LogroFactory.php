<?php

namespace Database\Factories\Academico;

use App\Models\Academico\Logro;
use App\Models\Academico\Competencia;
use App\Models\Evaluacion\Periodo;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogroFactory extends Factory
{
    protected $model = Logro::class;

    public function definition(): array
    {
        $logros = [
            'Identifica y clasifica los números naturales según sus propiedades',
            'Produce textos narrativos siguiendo la estructura adecuada',
            'Comprende y utiliza vocabulario básico en inglés',
            'Reconoce las partes de la célula y sus funciones',
            'Ubica geográficamente los departamentos de Colombia',
            'Demuestra habilidades motrices básicas en actividades físicas',
            'Aplica técnicas artísticas en la creación de obras',
            'Utiliza herramientas digitales para la creación de contenidos',
            'Practica valores de convivencia en el aula',
            'Desarrolla pensamiento lógico mediante resolución de problemas',
        ];

        return [
            'competencia_id' => Competencia::factory(),
            'periodo_id' => Periodo::factory(),
            'codigo' => fake()->unique()->lexify('LOG-???'),
            'descripcion' => fake()->randomElement($logros),
            'porcentaje' => fake()->randomElement([20, 25, 30, 33.33, 50]),
            'orden' => fake()->numberBetween(1, 5),
            'estado' => true,
        ];
    }
}

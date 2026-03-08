<?php

namespace Database\Factories\Academico;

use App\Models\Academico\Competencia;
use App\Models\Academico\AsignaturaGrado;
use App\Models\Academico\TipoCompetencia;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetenciaFactory extends Factory
{
    protected $model = Competencia::class;

    public function definition(): array
    {
        $competencias = [
            'Resuelve problemas matemáticos aplicando operaciones básicas',
            'Interpreta textos narrativos identificando personajes y eventos',
            'Argumenta sus ideas de manera clara y respetuosa',
            'Identifica las características de los seres vivos',
            'Reconoce los derechos y deberes ciudadanos',
            'Utiliza herramientas tecnológicas de manera responsable',
            'Produce textos escritos con coherencia y cohesión',
            'Analiza información estadística y gráfica',
            'Comprende fenómenos físicos del entorno',
            'Valora la diversidad cultural y social',
        ];

        return [
            'asignatura_grado_id' => AsignaturaGrado::factory(),
            'tipo_id' => TipoCompetencia::factory(),
            'codigo' => fake()->unique()->lexify('COMP-???'),
            'descripcion' => fake()->randomElement($competencias),
            'orden' => fake()->numberBetween(1, 10),
            'estado' => true,
        ];
    }
}

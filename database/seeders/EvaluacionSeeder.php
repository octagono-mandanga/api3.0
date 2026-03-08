<?php

namespace Database\Seeders;

use App\Models\Core\Institucion;
use App\Models\Core\Lectivo;
use App\Models\Evaluacion\Periodo;
use App\Models\Evaluacion\EscalaCalificacion;
use App\Models\Evaluacion\RangoEscala;
use Illuminate\Database\Seeder;

class EvaluacionSeeder extends Seeder
{
    public function run(): void
    {
        $instituciones = Institucion::all();

        foreach ($instituciones as $institucion) {
            $this->seedInstitucion($institucion);
        }
    }

    private function seedInstitucion(Institucion $institucion): void
    {
        $lectivo = Lectivo::where('institucion_id', $institucion->id)
            ->where('es_actual', true)
            ->first();

        if (!$lectivo) return;

        // Escala de calificación colombiana (1.0 - 5.0)
        // Campos EscalaCalificacion: institucion_id, nombre, nota_minima, nota_maxima, nota_aprobacion, usa_decimales, es_default, estado
        $escala = EscalaCalificacion::create([
            'institucion_id' => $institucion->id,
            'nombre' => 'Escala Nacional 1.0 - 5.0',
            'nota_minima' => 1.0,
            'nota_maxima' => 5.0,
            'nota_aprobacion' => 3.0,
            'usa_decimales' => true,
            'es_default' => true,
            'estado' => 'activo',
        ]);

        // Rangos de la escala (Desempeños)
        // Campos RangoEscala: escala_id, desde, hasta, desempeno, abreviatura, color (SIN estado)
        $rangos = [
            ['desde' => 1.0, 'hasta' => 2.9, 'desempeno' => 'Desempeño Bajo', 'abreviatura' => 'DBJ', 'color' => '#FF5252'],
            ['desde' => 3.0, 'hasta' => 3.9, 'desempeno' => 'Desempeño Básico', 'abreviatura' => 'DBA', 'color' => '#FFC107'],
            ['desde' => 4.0, 'hasta' => 4.5, 'desempeno' => 'Desempeño Alto', 'abreviatura' => 'DAL', 'color' => '#4CAF50'],
            ['desde' => 4.6, 'hasta' => 5.0, 'desempeno' => 'Desempeño Superior', 'abreviatura' => 'DSU', 'color' => '#2196F3'],
        ];

        foreach ($rangos as $rango) {
            RangoEscala::create([
                'escala_id' => $escala->id,
                'desde' => $rango['desde'],
                'hasta' => $rango['hasta'],
                'desempeno' => $rango['desempeno'],
                'abreviatura' => $rango['abreviatura'],
                'color' => $rango['color'],
            ]);
        }

        // Períodos académicos (4 períodos)
        // Campos Periodo: institucion_id, lectivo_id, numero, nombre, peso, fecha_inicio, fecha_fin, es_activo, estado
        $periodos = [
            [
                'numero' => 1,
                'nombre' => 'Primer Período',
                'peso' => 25.00,
                'fecha_inicio' => '2026-01-20',
                'fecha_fin' => '2026-04-03',
                'es_activo' => true,
            ],
            [
                'numero' => 2,
                'nombre' => 'Segundo Período',
                'peso' => 25.00,
                'fecha_inicio' => '2026-04-13',
                'fecha_fin' => '2026-06-19',
                'es_activo' => false,
            ],
            [
                'numero' => 3,
                'nombre' => 'Tercer Período',
                'peso' => 25.00,
                'fecha_inicio' => '2026-07-13',
                'fecha_fin' => '2026-09-18',
                'es_activo' => false,
            ],
            [
                'numero' => 4,
                'nombre' => 'Cuarto Período',
                'peso' => 25.00,
                'fecha_inicio' => '2026-09-28',
                'fecha_fin' => '2026-11-27',
                'es_activo' => false,
            ],
        ];

        foreach ($periodos as $periodo) {
            Periodo::create([
                'institucion_id' => $institucion->id,
                'lectivo_id' => $lectivo->id,
                'numero' => $periodo['numero'],
                'nombre' => $periodo['nombre'],
                'peso' => $periodo['peso'],
                'fecha_inicio' => $periodo['fecha_inicio'],
                'fecha_fin' => $periodo['fecha_fin'],
                'es_activo' => $periodo['es_activo'],
                'estado' => 'activo',
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Core\Institucion;
use App\Models\Core\Grado;
use App\Models\Academico\AreaFormacion;
use App\Models\Academico\Asignatura;
use App\Models\Academico\AsignaturaGrado;
use App\Models\Academico\TipoCompetencia;
use App\Models\Academico\Competencia;
use App\Models\Academico\Logro;
use Illuminate\Database\Seeder;

class AcademicoSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // DATOS GLOBALES (sin institucion_id)
        // ============================================

        // Áreas de formación - GLOBALES (campos: nombre, codigo, estado)
        $areas = [
            ['nombre' => 'Matemáticas', 'codigo' => 'MAT', 'estado' => 'activo'],
            ['nombre' => 'Humanidades y Lengua Castellana', 'codigo' => 'LEN', 'estado' => 'activo'],
            ['nombre' => 'Idioma Extranjero', 'codigo' => 'ING', 'estado' => 'activo'],
            ['nombre' => 'Ciencias Naturales', 'codigo' => 'NAT', 'estado' => 'activo'],
            ['nombre' => 'Ciencias Sociales', 'codigo' => 'SOC', 'estado' => 'activo'],
            ['nombre' => 'Educación Artística', 'codigo' => 'ART', 'estado' => 'activo'],
            ['nombre' => 'Educación Física', 'codigo' => 'EFI', 'estado' => 'activo'],
            ['nombre' => 'Tecnología e Informática', 'codigo' => 'TEC', 'estado' => 'activo'],
            ['nombre' => 'Educación Religiosa', 'codigo' => 'REL', 'estado' => 'activo'],
            ['nombre' => 'Ética y Valores', 'codigo' => 'ETI', 'estado' => 'activo'],
        ];

        $areasCreadas = [];
        foreach ($areas as $area) {
            $areasCreadas[$area['codigo']] = AreaFormacion::create($area);
        }

        // Tipos de competencia - GLOBALES (campos: nombre, codigo, estado)
        $tiposCompetencia = [
            ['nombre' => 'Competencias del Ser', 'codigo' => 'SER', 'estado' => 'activo'],
            ['nombre' => 'Competencias del Saber', 'codigo' => 'SAB', 'estado' => 'activo'],
            ['nombre' => 'Competencias del Hacer', 'codigo' => 'HAC', 'estado' => 'activo'],
        ];

        $tiposCreados = [];
        foreach ($tiposCompetencia as $tipo) {
            $tiposCreados[$tipo['codigo']] = TipoCompetencia::create($tipo);
        }

        // ============================================
        // DATOS POR INSTITUCIÓN
        // ============================================
        $instituciones = Institucion::all();

        foreach ($instituciones as $institucion) {
            $this->crearAsignaturasParaInstitucion($institucion, $areasCreadas, $tiposCreados);
        }
    }

    private function crearAsignaturasParaInstitucion(Institucion $institucion, array $areasCreadas, array $tiposCreados): void
    {
        // Definición de asignaturas con su área correspondiente
        // Campos Asignatura: institucion_id, area_id, nombre, codigo, descripcion, horas_semanales, es_obligatoria, estado
        $asignaturasDefinicion = [
            'MAT' => [
                ['nombre' => 'Matemáticas', 'codigo' => 'MAT', 'horas_semanales' => 5, 'es_obligatoria' => true],
                ['nombre' => 'Geometría', 'codigo' => 'GEO', 'horas_semanales' => 2, 'es_obligatoria' => true],
                ['nombre' => 'Estadística', 'codigo' => 'EST', 'horas_semanales' => 2, 'es_obligatoria' => true],
            ],
            'LEN' => [
                ['nombre' => 'Lengua Castellana', 'codigo' => 'ESP', 'horas_semanales' => 5, 'es_obligatoria' => true],
                ['nombre' => 'Plan Lector', 'codigo' => 'LEC', 'horas_semanales' => 2, 'es_obligatoria' => false],
            ],
            'ING' => [
                ['nombre' => 'Inglés', 'codigo' => 'ING', 'horas_semanales' => 4, 'es_obligatoria' => true],
            ],
            'NAT' => [
                ['nombre' => 'Ciencias Naturales', 'codigo' => 'NAT', 'horas_semanales' => 4, 'es_obligatoria' => true],
                ['nombre' => 'Biología', 'codigo' => 'BIO', 'horas_semanales' => 3, 'es_obligatoria' => true],
                ['nombre' => 'Química', 'codigo' => 'QUI', 'horas_semanales' => 3, 'es_obligatoria' => true],
                ['nombre' => 'Física', 'codigo' => 'FIS', 'horas_semanales' => 3, 'es_obligatoria' => true],
            ],
            'SOC' => [
                ['nombre' => 'Ciencias Sociales', 'codigo' => 'SOC', 'horas_semanales' => 4, 'es_obligatoria' => true],
                ['nombre' => 'Historia', 'codigo' => 'HIS', 'horas_semanales' => 2, 'es_obligatoria' => true],
                ['nombre' => 'Geografía', 'codigo' => 'GEG', 'horas_semanales' => 2, 'es_obligatoria' => true],
                ['nombre' => 'Filosofía', 'codigo' => 'FIL', 'horas_semanales' => 2, 'es_obligatoria' => true],
                ['nombre' => 'Economía y Política', 'codigo' => 'ECO', 'horas_semanales' => 2, 'es_obligatoria' => true],
            ],
            'ART' => [
                ['nombre' => 'Artes', 'codigo' => 'ART', 'horas_semanales' => 2, 'es_obligatoria' => true],
                ['nombre' => 'Música', 'codigo' => 'MUS', 'horas_semanales' => 2, 'es_obligatoria' => false],
            ],
            'EFI' => [
                ['nombre' => 'Educación Física', 'codigo' => 'EFI', 'horas_semanales' => 2, 'es_obligatoria' => true],
            ],
            'TEC' => [
                ['nombre' => 'Tecnología', 'codigo' => 'TEC', 'horas_semanales' => 2, 'es_obligatoria' => true],
                ['nombre' => 'Informática', 'codigo' => 'INF', 'horas_semanales' => 2, 'es_obligatoria' => true],
            ],
            'REL' => [
                ['nombre' => 'Religión', 'codigo' => 'REL', 'horas_semanales' => 1, 'es_obligatoria' => false],
            ],
            'ETI' => [
                ['nombre' => 'Ética y Valores', 'codigo' => 'ETI', 'horas_semanales' => 1, 'es_obligatoria' => true],
            ],
        ];

        // Obtener grados (solo los que la institución tiene via GradoInstitucion)
        $gradosInstitucion = $institucion->gradosInstitucion()
            ->with('grado')
            ->where('estado', 'activo')
            ->get()
            ->pluck('grado');

        // Crear asignaturas para esta institución
        foreach ($asignaturasDefinicion as $areaCodigo => $asignaturas) {
            $area = $areasCreadas[$areaCodigo];

            foreach ($asignaturas as $asigData) {
                $asignatura = Asignatura::create([
                    'institucion_id' => $institucion->id,
                    'area_id' => $area->id,
                    'nombre' => $asigData['nombre'],
                    'codigo' => $asigData['codigo'] . '-' . substr($institucion->id, 0, 8),
                    'descripcion' => "Asignatura de {$asigData['nombre']} para {$institucion->nombre_legal}",
                    'horas_semanales' => $asigData['horas_semanales'],
                    'es_obligatoria' => $asigData['es_obligatoria'],
                    'estado' => 'activo',
                ]);

                // Asignar a grados correspondientes
                // Campos AsignaturaGrado: asignatura_id, grado_id, intensidad_horaria, estado
                foreach ($gradosInstitucion as $grado) {
                    if ($this->debeAsignarAGrado($asigData['codigo'], $grado->id)) {
                        AsignaturaGrado::create([
                            'asignatura_id' => $asignatura->id,
                            'grado_id' => $grado->id,
                            'intensidad_horaria' => $asigData['horas_semanales'],
                            'estado' => 'activo',
                        ]);

                        // Crear competencias para esta asignatura
                        // Campos Competencia: institucion_id, tipo_id, asignatura_id, nombre, codigo, descripcion, estado
                        foreach ($tiposCreados as $tipoCodigo => $tipoComp) {
                            $competencia = Competencia::create([
                                'institucion_id' => $institucion->id,
                                'tipo_id' => $tipoComp->id,
                                'asignatura_id' => $asignatura->id,
                                'nombre' => "Competencia {$tipoComp->nombre} - {$asigData['nombre']} Grado {$grado->nombre}",
                                'codigo' => "{$asigData['codigo']}-G{$grado->id}-{$tipoCodigo}",
                                'descripcion' => "Competencia de {$tipoComp->nombre} para {$asigData['nombre']}",
                                'estado' => 'activo',
                            ]);


                            // Campos Logro: institucion_id, competencia_id, asignatura_id, grado_id, descripcion, codigo, estado
                            $numLogros = rand(2, 3);
                            for ($i = 1; $i <= $numLogros; $i++) {
                                Logro::create([
                                    'institucion_id' => $institucion->id,
                                    'competencia_id' => $competencia->id,
                                    'asignatura_id' => $asignatura->id,
                                    'grado_id' => $grado->id,
                                    'descripcion' => $this->generarDescripcionLogro($asigData['nombre'], $tipoCodigo, $i),
                                    'codigo' => "{$competencia->codigo}-L{$i}",
                                    'estado' => 'activo',
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function debeAsignarAGrado(string $codigoAsig, int $gradoId): bool
    {
        // Grados: 1-3 preescolar, 4-8 primaria, 9-12 secundaria, 13-14 media
        $asignaturasSecundaria = ['QUI', 'FIS', 'BIO'];
        $asignaturasMedia = ['FIL', 'ECO'];

        if (in_array($codigoAsig, $asignaturasSecundaria) && $gradoId < 9) {
            return false;
        }

        if (in_array($codigoAsig, $asignaturasMedia) && $gradoId < 13) {
            return false;
        }

        // Ciencias Naturales generales solo hasta 8vo
        if ($codigoAsig === 'NAT' && $gradoId > 8) {
            return false;
        }

        // Geometría y Estadística desde 4to
        if (in_array($codigoAsig, ['GEO', 'EST']) && $gradoId < 4) {
            return false;
        }

        return true;
    }

    private function generarDescripcionLogro(string $asignatura, string $tipoComp, int $numero): string
    {
        $verbos = [
            'SER' => ['Demuestra', 'Manifiesta', 'Valora', 'Asume', 'Respeta'],
            'SAB' => ['Identifica', 'Reconoce', 'Comprende', 'Analiza', 'Explica'],
            'HAC' => ['Aplica', 'Desarrolla', 'Construye', 'Elabora', 'Resuelve'],
        ];

        $complementos = [
            "los conceptos fundamentales de {$asignatura}",
            "las habilidades propias de {$asignatura}",
            "el trabajo colaborativo en actividades de {$asignatura}",
            "la importancia de {$asignatura} en la vida cotidiana",
            "los procedimientos básicos de {$asignatura}",
        ];

        $verbo = $verbos[$tipoComp][array_rand($verbos[$tipoComp])];
        $complemento = $complementos[($numero - 1) % count($complementos)];

        return "{$verbo} {$complemento}.";
    }
}

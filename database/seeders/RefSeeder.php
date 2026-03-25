<?php

namespace Database\Seeders;

use App\Models\Ref\TipoDocumento;
use App\Models\Ref\Etnia;
use App\Models\Ref\Discapacidad;
use App\Models\Ref\Eps;
use Illuminate\Database\Seeder;

class RefSeeder extends Seeder
{
    public function run(): void
    {
        // Tipos de documento (solo: nombre, codigo, estado)
        $tiposDocumento = [
            ['codigo' => 'CC', 'nombre' => 'Cédula de Ciudadanía', 'estado' => 'activo'],
            ['codigo' => 'TI', 'nombre' => 'Tarjeta de Identidad', 'estado' => 'activo'],
            ['codigo' => 'RC', 'nombre' => 'Registro Civil', 'estado' => 'activo'],
            ['codigo' => 'CE', 'nombre' => 'Cédula de Extranjería', 'estado' => 'activo'],
            ['codigo' => 'PA', 'nombre' => 'Pasaporte', 'estado' => 'activo'],
            ['codigo' => 'NUIP', 'nombre' => 'NUIP', 'estado' => 'activo'],
        ];

        foreach ($tiposDocumento as $tipo) {
            TipoDocumento::create($tipo);
        }

        // Departamentos y municipios completos de Colombia (33 dptos, ~1.122 municipios)
        $this->call(DepartamentosMunicipiosSeeder::class);

        // Etnias
        $etnias = [
            ['codigo' => 'NA', 'nombre' => 'No aplica', 'estado' => 'activo'],
            ['codigo' => 'IND', 'nombre' => 'Indígena', 'estado' => 'activo'],
            ['codigo' => 'ROM', 'nombre' => 'Rom (Gitano)', 'estado' => 'activo'],
            ['codigo' => 'RAI', 'nombre' => 'Raizal del Archipiélago de San Andrés y Providencia', 'estado' => 'activo'],
            ['codigo' => 'PAL', 'nombre' => 'Palenquero de San Basilio', 'estado' => 'activo'],
            ['codigo' => 'AFR', 'nombre' => 'Negro(a), Mulato(a), Afrocolombiano(a)', 'estado' => 'activo'],
        ];

        foreach ($etnias as $etnia) {
            Etnia::create($etnia);
        }

        // Discapacidades
        $discapacidades = [
            ['codigo' => 'NA', 'nombre' => 'No aplica', 'estado' => 'activo'],
            ['codigo' => 'FIS', 'nombre' => 'Física', 'estado' => 'activo'],
            ['codigo' => 'AUD', 'nombre' => 'Auditiva', 'estado' => 'activo'],
            ['codigo' => 'VIS', 'nombre' => 'Visual', 'estado' => 'activo'],
            ['codigo' => 'COG', 'nombre' => 'Cognitiva', 'estado' => 'activo'],
            ['codigo' => 'PSI', 'nombre' => 'Psicosocial', 'estado' => 'activo'],
            ['codigo' => 'MUL', 'nombre' => 'Múltiple', 'estado' => 'activo'],
            ['codigo' => 'SOR', 'nombre' => 'Sordoceguera', 'estado' => 'activo'],
        ];

        foreach ($discapacidades as $disc) {
            Discapacidad::create($disc);
        }

        // EPS
        $epsList = [
            ['codigo' => 'EPS001', 'nombre' => 'Sura EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS002', 'nombre' => 'Sanitas EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS003', 'nombre' => 'Nueva EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS004', 'nombre' => 'Salud Total EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS005', 'nombre' => 'Coomeva EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS006', 'nombre' => 'Compensar EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS007', 'nombre' => 'Famisanar EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS008', 'nombre' => 'Mutual Ser EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS009', 'nombre' => 'Coosalud EPS', 'estado' => 'activo'],
            ['codigo' => 'EPS010', 'nombre' => 'Capital Salud EPS', 'estado' => 'activo'],
        ];

        foreach ($epsList as $eps) {
            Eps::create($eps);
        }
    }
}

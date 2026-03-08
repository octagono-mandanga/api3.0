<?php

namespace Database\Seeders;

use App\Models\Ref\TipoDocumento;
use App\Models\Ref\Departamento;
use App\Models\Ref\Municipio;
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

        // Departamentos principales de Colombia
        $departamentos = [
            ['codigo' => '11', 'nombre' => 'Bogotá D.C.', 'estado' => 'activo'],
            ['codigo' => '05', 'nombre' => 'Antioquia', 'estado' => 'activo'],
            ['codigo' => '76', 'nombre' => 'Valle del Cauca', 'estado' => 'activo'],
            ['codigo' => '08', 'nombre' => 'Atlántico', 'estado' => 'activo'],
            ['codigo' => '13', 'nombre' => 'Bolívar', 'estado' => 'activo'],
            ['codigo' => '25', 'nombre' => 'Cundinamarca', 'estado' => 'activo'],
            ['codigo' => '68', 'nombre' => 'Santander', 'estado' => 'activo'],
            ['codigo' => '17', 'nombre' => 'Caldas', 'estado' => 'activo'],
            ['codigo' => '66', 'nombre' => 'Risaralda', 'estado' => 'activo'],
            ['codigo' => '54', 'nombre' => 'Norte de Santander', 'estado' => 'activo'],
        ];

        foreach ($departamentos as $depto) {
            Departamento::create($depto);
        }

        // Municipios principales
        $municipios = [
            // Bogotá
            ['departamento_id' => 1, 'codigo' => '11001', 'nombre' => 'Bogotá D.C.', 'estado' => 'activo'],
            // Antioquia
            ['departamento_id' => 2, 'codigo' => '05001', 'nombre' => 'Medellín', 'estado' => 'activo'],
            ['departamento_id' => 2, 'codigo' => '05088', 'nombre' => 'Bello', 'estado' => 'activo'],
            ['departamento_id' => 2, 'codigo' => '05360', 'nombre' => 'Itagüí', 'estado' => 'activo'],
            ['departamento_id' => 2, 'codigo' => '05266', 'nombre' => 'Envigado', 'estado' => 'activo'],
            // Valle del Cauca
            ['departamento_id' => 3, 'codigo' => '76001', 'nombre' => 'Cali', 'estado' => 'activo'],
            ['departamento_id' => 3, 'codigo' => '76109', 'nombre' => 'Buenaventura', 'estado' => 'activo'],
            ['departamento_id' => 3, 'codigo' => '76520', 'nombre' => 'Palmira', 'estado' => 'activo'],
            // Atlántico
            ['departamento_id' => 4, 'codigo' => '08001', 'nombre' => 'Barranquilla', 'estado' => 'activo'],
            ['departamento_id' => 4, 'codigo' => '08758', 'nombre' => 'Soledad', 'estado' => 'activo'],
            // Bolívar
            ['departamento_id' => 5, 'codigo' => '13001', 'nombre' => 'Cartagena', 'estado' => 'activo'],
            // Cundinamarca
            ['departamento_id' => 6, 'codigo' => '25754', 'nombre' => 'Soacha', 'estado' => 'activo'],
            ['departamento_id' => 6, 'codigo' => '25175', 'nombre' => 'Chía', 'estado' => 'activo'],
            ['departamento_id' => 6, 'codigo' => '25899', 'nombre' => 'Zipaquirá', 'estado' => 'activo'],
            // Santander
            ['departamento_id' => 7, 'codigo' => '68001', 'nombre' => 'Bucaramanga', 'estado' => 'activo'],
            ['departamento_id' => 7, 'codigo' => '68276', 'nombre' => 'Floridablanca', 'estado' => 'activo'],
        ];

        foreach ($municipios as $mun) {
            Municipio::create($mun);
        }

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

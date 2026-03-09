<?php

namespace Database\Seeders;

use App\Models\Auth\Usuario;
use App\Models\Auth\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // Roles del sistema
        $roles = [
            ['nombre' => 'Super Administrador', 'codigo' => 'root', 'descripcion' => 'Acceso total al sistema', 'es_sistema' => true, 'estado' => 'activo'],
            ['nombre' => 'Administrador', 'codigo' => 'manager', 'descripcion' => 'Administrador de institución', 'es_sistema' => true, 'estado' => 'activo'],
            ['nombre' => 'Rector', 'codigo' => 'rector', 'descripcion' => 'Rector de la institución', 'es_sistema' => false, 'estado' => 'activo'],
            ['nombre' => 'Coordinador', 'codigo' => 'academico', 'descripcion' => 'Coordinador académico', 'es_sistema' => false, 'estado' => 'activo'],
            ['nombre' => 'Disciplina', 'codigo' => 'disciplina', 'descripcion' => 'Encargado de disciplina', 'es_sistema' => false, 'estado' => 'activo'],
            ['nombre' => 'Docente', 'codigo' => 'docente', 'descripcion' => 'Docente de la institución', 'es_sistema' => false, 'estado' => 'activo'],
            ['nombre' => 'Estudiante', 'codigo' => 'alumno', 'descripcion' => 'Estudiante matriculado', 'es_sistema' => false, 'estado' => 'activo'],
            ['nombre' => 'Acudiente', 'codigo' => 'acudiente', 'descripcion' => 'Acudiente o padre de familia', 'es_sistema' => false, 'estado' => 'activo'],
            ['nombre' => 'Secretaria', 'codigo' => 'secretaria', 'descripcion' => 'Personal administrativo', 'es_sistema' => false, 'estado' => 'activo'],

        ];

        foreach ($roles as $rol) {
            Rol::create($rol);
        }

        // Usuario Super Admin
        Usuario::create([
            'tipo_documento_id' => 1, // CC
            'numero_documento' => '1234567890',
            'primer_nombre' => 'Super',
            'segundo_nombre' => null,
            'primer_apellido' => 'Administrador',
            'segundo_apellido' => 'Sistema',
            'email' => 'admin@sistema.com',
            'email_verificado_en' => now(),
            'password' => Hash::make('admin123'),
            'telefono' => '6011234567',
            'celular' => '3101234567',
            'direccion' => 'Calle 100 # 10-20',
            'fecha_nacimiento' => '1980-01-15',
            'genero' => 'M',
            'municipio_id' => 1, // Bogotá
            'estado' => 'activo',
        ]);
    }
}

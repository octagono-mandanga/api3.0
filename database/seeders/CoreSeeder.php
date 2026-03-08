<?php

namespace Database\Seeders;

use App\Models\Core\Plan;
use App\Models\Core\Institucion;
use App\Models\Core\Sede;
use App\Models\Core\NivelEducativo;
use App\Models\Core\Grado;
use App\Models\Core\Jornada;
use App\Models\Core\ModeloEducativo;
use App\Models\Core\Lectivo;
use App\Models\Core\RolInstitucion;
use App\Models\Core\NivelInstitucion;
use App\Models\Core\GradoInstitucion;
use App\Models\Core\Perfil;
use App\Models\Auth\Rol;
use App\Models\Auth\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreSeeder extends Seeder
{
    public function run(): void
    {
        // Planes de suscripción (campos: nombre, codigo, descripcion, max_usuarios, max_estudiantes, estado)
        $planBasico = Plan::create([
            'nombre' => 'Plan Básico',
            'codigo' => 'BASICO',
            'descripcion' => 'Plan básico para instituciones pequeñas',
            'max_estudiantes' => 500,
            'max_usuarios' => 100,
            'estado' => 'activo',
        ]);

        $planPremium = Plan::create([
            'nombre' => 'Plan Premium',
            'codigo' => 'PREMIUM',
            'descripcion' => 'Plan completo para instituciones grandes',
            'max_estudiantes' => 2000,
            'max_usuarios' => 500,
            'estado' => 'activo',
        ]);

        // Niveles educativos (campos: nombre, codigo, orden, estado)
        $niveles = [
            ['nombre' => 'Preescolar', 'codigo' => 'PRE', 'orden' => 1, 'estado' => 'activo'],
            ['nombre' => 'Básica Primaria', 'codigo' => 'PRI', 'orden' => 2, 'estado' => 'activo'],
            ['nombre' => 'Básica Secundaria', 'codigo' => 'SEC', 'orden' => 3, 'estado' => 'activo'],
            ['nombre' => 'Media', 'codigo' => 'MED', 'orden' => 4, 'estado' => 'activo'],
        ];

        foreach ($niveles as $nivel) {
            NivelEducativo::create($nivel);
        }

        // Grados (campos: nivel_id, nombre, codigo, orden, estado)
        $grados = [
            // Preescolar (nivel_id = 1)
            ['nivel_id' => 1, 'nombre' => 'Prejardín', 'codigo' => 'PRE', 'orden' => 1, 'estado' => 'activo'],
            ['nivel_id' => 1, 'nombre' => 'Jardín', 'codigo' => 'JAR', 'orden' => 2, 'estado' => 'activo'],
            ['nivel_id' => 1, 'nombre' => 'Transición', 'codigo' => 'TRA', 'orden' => 3, 'estado' => 'activo'],
            // Primaria (nivel_id = 2)
            ['nivel_id' => 2, 'nombre' => 'Primero', 'codigo' => '1', 'orden' => 4, 'estado' => 'activo'],
            ['nivel_id' => 2, 'nombre' => 'Segundo', 'codigo' => '2', 'orden' => 5, 'estado' => 'activo'],
            ['nivel_id' => 2, 'nombre' => 'Tercero', 'codigo' => '3', 'orden' => 6, 'estado' => 'activo'],
            ['nivel_id' => 2, 'nombre' => 'Cuarto', 'codigo' => '4', 'orden' => 7, 'estado' => 'activo'],
            ['nivel_id' => 2, 'nombre' => 'Quinto', 'codigo' => '5', 'orden' => 8, 'estado' => 'activo'],
            // Secundaria (nivel_id = 3)
            ['nivel_id' => 3, 'nombre' => 'Sexto', 'codigo' => '6', 'orden' => 9, 'estado' => 'activo'],
            ['nivel_id' => 3, 'nombre' => 'Séptimo', 'codigo' => '7', 'orden' => 10, 'estado' => 'activo'],
            ['nivel_id' => 3, 'nombre' => 'Octavo', 'codigo' => '8', 'orden' => 11, 'estado' => 'activo'],
            ['nivel_id' => 3, 'nombre' => 'Noveno', 'codigo' => '9', 'orden' => 12, 'estado' => 'activo'],
            // Media (nivel_id = 4)
            ['nivel_id' => 4, 'nombre' => 'Décimo', 'codigo' => '10', 'orden' => 13, 'estado' => 'activo'],
            ['nivel_id' => 4, 'nombre' => 'Undécimo', 'codigo' => '11', 'orden' => 14, 'estado' => 'activo'],
        ];

        foreach ($grados as $grado) {
            Grado::create($grado);
        }

        // Jornadas (campos: nombre, hora_inicio, hora_fin, estado) - SIN codigo
        $jornadas = [
            ['nombre' => 'Mañana', 'hora_inicio' => '06:30:00', 'hora_fin' => '12:30:00', 'estado' => 'activo'],
            ['nombre' => 'Tarde', 'hora_inicio' => '12:30:00', 'hora_fin' => '18:30:00', 'estado' => 'activo'],
            ['nombre' => 'Completa', 'hora_inicio' => '06:30:00', 'hora_fin' => '15:00:00', 'estado' => 'activo'],
            ['nombre' => 'Nocturna', 'hora_inicio' => '18:00:00', 'hora_fin' => '22:00:00', 'estado' => 'activo'],
            ['nombre' => 'Fin de Semana', 'hora_inicio' => '07:00:00', 'hora_fin' => '17:00:00', 'estado' => 'activo'],
        ];

        foreach ($jornadas as $jornada) {
            Jornada::create($jornada);
        }

        // Modelos educativos (campos: nombre, descripcion, estado) - SIN codigo
        $modelos = [
            ['nombre' => 'Tradicional', 'descripcion' => 'Modelo educativo tradicional', 'estado' => 'activo'],
            ['nombre' => 'Escuela Nueva', 'descripcion' => 'Modelo flexible para zonas rurales', 'estado' => 'activo'],
            ['nombre' => 'Aceleración del Aprendizaje', 'descripcion' => 'Para estudiantes en extra-edad', 'estado' => 'activo'],
            ['nombre' => 'Cafam', 'descripcion' => 'Educación continuada para adultos', 'estado' => 'activo'],
        ];

        foreach ($modelos as $modelo) {
            ModeloEducativo::create($modelo);
        }

        // ============================================
        // INSTITUCIÓN 1: Colegio San José (3 sedes)
        // ============================================
        // Campos Institucion: plan_id, tema_id, municipio_id, nit, codigo_dane, tipo_institucion, nombre_legal, nombre_corto,
        //                     direccion, telefono, email_oficial, sitio_web, dominio, logo_url, portada_url, rector_id, colores_marca, estado
        $institucion1 = Institucion::create([
            'plan_id' => $planPremium->id,
            'municipio_id' => 1, // Bogotá
            'nit' => '900123456-1',
            'codigo_dane' => '111001000001',
            'tipo_institucion' => 'privado',
            'nombre_legal' => 'Colegio San José',
            'nombre_corto' => 'Col. San José',
            'direccion' => 'Carrera 15 # 85-30',
            'telefono' => '6012345678',
            'email_oficial' => 'contacto@colegiosanjose.edu.co',
            'sitio_web' => 'https://colegiosanjose.edu.co',
            'estado' => 'activo',
        ]);

        // Sedes de Institución 1 (campos: institucion_id, municipio_id, nombre, codigo, es_principal, direccion, telefono, estado)
        $sede1_1 = Sede::create([
            'institucion_id' => $institucion1->id,
            'municipio_id' => 1, // Bogotá
            'nombre' => 'Sede Principal',
            'codigo' => 'PRINCIPAL',
            'es_principal' => true,
            'direccion' => 'Carrera 15 # 85-30',
            'telefono' => '6012345678',
            'estado' => 'activo',
        ]);

        $sede1_2 = Sede::create([
            'institucion_id' => $institucion1->id,
            'municipio_id' => 1, // Bogotá
            'nombre' => 'Sede Norte',
            'codigo' => 'NORTE',
            'es_principal' => false,
            'direccion' => 'Calle 170 # 50-20',
            'telefono' => '6012345679',
            'estado' => 'activo',
        ]);

        $sede1_3 = Sede::create([
            'institucion_id' => $institucion1->id,
            'municipio_id' => 13, // Chía (Cundinamarca)
            'nombre' => 'Sede Chía',
            'codigo' => 'CHIA',
            'es_principal' => false,
            'direccion' => 'Avenida Pradilla # 2-100',
            'telefono' => '6018612345',
            'estado' => 'activo',
        ]);

        // ============================================
        // INSTITUCIÓN 2: I.E. Antonio Nariño (1 sede)
        // ============================================
        $institucion2 = Institucion::create([
            'plan_id' => $planBasico->id,
            'municipio_id' => 2, // Medellín
            'nit' => '800987654-2',
            'codigo_dane' => '105001000002',
            'tipo_institucion' => 'oficial',
            'nombre_legal' => 'Institución Educativa Antonio Nariño',
            'nombre_corto' => 'I.E. Antonio Nariño',
            'direccion' => 'Calle 50 # 45-30',
            'telefono' => '6044567890',
            'email_oficial' => 'contacto@ieantonionarino.edu.co',
            'estado' => 'activo',
        ]);

        $sede2_1 = Sede::create([
            'institucion_id' => $institucion2->id,
            'municipio_id' => 2, // Medellín
            'nombre' => 'Sede Principal',
            'codigo' => 'UNICA',
            'es_principal' => true,
            'direccion' => 'Calle 50 # 45-30',
            'telefono' => '6044567890',
            'estado' => 'activo',
        ]);

        // Años Lectivos (campos: institucion_id, anio, nombre, fecha_inicio, fecha_fin, es_actual, estado)
        $lectivo1 = Lectivo::create([
            'institucion_id' => $institucion1->id,
            'anio' => 2026,
            'nombre' => 'Año Lectivo 2026',
            'fecha_inicio' => '2026-01-20',
            'fecha_fin' => '2026-11-30',
            'es_actual' => true,
            'estado' => 'activo',
        ]);

        $lectivo2 = Lectivo::create([
            'institucion_id' => $institucion2->id,
            'anio' => 2026,
            'nombre' => 'Año Lectivo 2026',
            'fecha_inicio' => '2026-01-27',
            'fecha_fin' => '2026-11-28',
            'es_actual' => true,
            'estado' => 'activo',
        ]);

        // Roles por institución (campos: institucion_id, rol_id, alias, permisos_extra, estado)
        $rolesDelSistema = Rol::all();
        foreach ($rolesDelSistema as $rol) {
            RolInstitucion::create([
                'institucion_id' => $institucion1->id,
                'rol_id' => $rol->id,
                'estado' => 'activo',
            ]);
            RolInstitucion::create([
                'institucion_id' => $institucion2->id,
                'rol_id' => $rol->id,
                'estado' => 'activo',
            ]);
        }

        // Niveles por institución (campos: institucion_id, nivel_id, estado)
        // Institución 1: Todos los niveles
        foreach ([1, 2, 3, 4] as $nivelId) {
            NivelInstitucion::create([
                'institucion_id' => $institucion1->id,
                'nivel_id' => $nivelId,
                'estado' => 'activo',
            ]);
        }

        // Institución 2: Solo Primaria y Secundaria
        foreach ([2, 3] as $nivelId) {
            NivelInstitucion::create([
                'institucion_id' => $institucion2->id,
                'nivel_id' => $nivelId,
                'estado' => 'activo',
            ]);
        }

        // Grados por institución (campos: institucion_id, grado_id, alias, estado)
        // Institución 1: Todos los grados
        foreach (range(1, 14) as $gradoId) {
            GradoInstitucion::create([
                'institucion_id' => $institucion1->id,
                'grado_id' => $gradoId,
                'estado' => 'activo',
            ]);
        }

        // Institución 2: Primero a Noveno (grados 4-12)
        foreach (range(4, 12) as $gradoId) {
            GradoInstitucion::create([
                'institucion_id' => $institucion2->id,
                'grado_id' => $gradoId,
                'estado' => 'activo',
            ]);
        }

        // ============================================
        // USUARIOS ADMINISTRATIVOS - Institución 1
        // ============================================
        $rector1 = Usuario::create([
            'tipo_documento_id' => 1, // CC
            'numero_documento' => '51234567',
            'primer_nombre' => 'María',
            'segundo_nombre' => 'Elena',
            'primer_apellido' => 'González',
            'segundo_apellido' => 'Ramírez',
            'email' => 'rectoria@colegiosanjose.edu.co',
            'email_verificado_en' => now(),
            'password' => Hash::make('rector123'),
            'celular' => '3151234567',
            'direccion' => 'Carrera 20 # 100-50',
            'fecha_nacimiento' => '1970-05-20',
            'genero' => 'F',
            'municipio_id' => 1,
            'estado' => 'activo',
        ]);

        // Actualizar rector en institución
        $institucion1->update(['rector_id' => $rector1->id]);

        $coordinador1 = Usuario::create([
            'tipo_documento_id' => 1,
            'numero_documento' => '79876543',
            'primer_nombre' => 'Carlos',
            'segundo_nombre' => 'Alberto',
            'primer_apellido' => 'Martínez',
            'segundo_apellido' => 'López',
            'email' => 'coordinacion@colegiosanjose.edu.co',
            'email_verificado_en' => now(),
            'password' => Hash::make('coord123'),
            'celular' => '3109876543',
            'direccion' => 'Calle 85 # 15-30',
            'fecha_nacimiento' => '1975-08-15',
            'genero' => 'M',
            'municipio_id' => 1,
            'estado' => 'activo',
        ]);

        // Perfiles (campos: usuario_id, institucion_id, sede_id, rol_id, es_principal, estado)
        $rolRector = Rol::where('codigo', 'RECTOR')->first();
        $rolCoordinador = Rol::where('codigo', 'COORDINADOR')->first();

        Perfil::create([
            'usuario_id' => $rector1->id,
            'institucion_id' => $institucion1->id,
            'sede_id' => $sede1_1->id,
            'rol_id' => $rolRector->id,
            'es_principal' => true,
            'estado' => 'activo',
        ]);

        Perfil::create([
            'usuario_id' => $coordinador1->id,
            'institucion_id' => $institucion1->id,
            'sede_id' => $sede1_1->id,
            'rol_id' => $rolCoordinador->id,
            'es_principal' => true,
            'estado' => 'activo',
        ]);

        // ============================================
        // USUARIOS ADMINISTRATIVOS - Institución 2
        // ============================================
        $rector2 = Usuario::create([
            'tipo_documento_id' => 1,
            'numero_documento' => '71234890',
            'primer_nombre' => 'Jorge',
            'segundo_nombre' => 'Luis',
            'primer_apellido' => 'Pérez',
            'segundo_apellido' => 'Gómez',
            'email' => 'rectoria@ieantonionarino.edu.co',
            'email_verificado_en' => now(),
            'password' => Hash::make('rector123'),
            'celular' => '3201234567',
            'direccion' => 'Carrera 70 # 30-15',
            'fecha_nacimiento' => '1968-11-10',
            'genero' => 'M',
            'municipio_id' => 2,
            'estado' => 'activo',
        ]);

        $institucion2->update(['rector_id' => $rector2->id]);

        $coordinador2 = Usuario::create([
            'tipo_documento_id' => 1,
            'numero_documento' => '43876543',
            'primer_nombre' => 'Ana',
            'segundo_nombre' => 'María',
            'primer_apellido' => 'Restrepo',
            'segundo_apellido' => 'Valencia',
            'email' => 'coordinacion@ieantonionarino.edu.co',
            'email_verificado_en' => now(),
            'password' => Hash::make('coord123'),
            'celular' => '3157654321',
            'direccion' => 'Calle 45 # 80-20',
            'fecha_nacimiento' => '1980-03-25',
            'genero' => 'F',
            'municipio_id' => 2,
            'estado' => 'activo',
        ]);

        Perfil::create([
            'usuario_id' => $rector2->id,
            'institucion_id' => $institucion2->id,
            'sede_id' => $sede2_1->id,
            'rol_id' => $rolRector->id,
            'es_principal' => true,
            'estado' => 'activo',
        ]);

        Perfil::create([
            'usuario_id' => $coordinador2->id,
            'institucion_id' => $institucion2->id,
            'sede_id' => $sede2_1->id,
            'rol_id' => $rolCoordinador->id,
            'es_principal' => true,
            'estado' => 'activo',
        ]);
    }
}

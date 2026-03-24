<?php

namespace App\Console\Commands;

use App\Models\Auth\Usuario;
use App\Models\Auth\Rol;
use App\Models\Core\Perfil;
use Illuminate\Console\Command;

class CreateAdminProfile extends Command
{
    protected $signature = 'admin:create-profile {--email=admin@sistema.com}';
    protected $description = 'Crear el perfil root para el usuario administrador';

    public function handle()
    {
        $email = $this->option('email');
        
        $admin = Usuario::where('email', $email)->first();
        if (!$admin) {
            $this->error("Usuario con email {$email} no encontrado");
            return 1;
        }

        $rolRoot = Rol::where('codigo', 'root')->first();
        if (!$rolRoot) {
            $this->error("Rol 'root' no encontrado. Ejecute primero el seeder de roles.");
            return 1;
        }

        // Verificar si ya existe el perfil
        $perfilExistente = Perfil::where('usuario_id', $admin->id)
            ->where('rol_id', $rolRoot->id)
            ->whereNull('institucion_id')
            ->first();

        if ($perfilExistente) {
            $this->info("El perfil root ya existe para {$email}");
            return 0;
        }

        // Crear el perfil
        try {
            Perfil::create([
                'usuario_id' => $admin->id,
                'rol_id' => $rolRoot->id,
                'institucion_id' => null,
                'es_principal' => true,
                'estado' => 'activo',
            ]);
            $this->info("Perfil root creado exitosamente para {$email}");
        } catch (\Exception $e) {
            $this->error("Error creando perfil: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

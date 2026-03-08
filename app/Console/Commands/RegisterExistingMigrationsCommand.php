<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Comando para registrar como ejecutadas todas las migraciones
 * cuyas tablas ya existen en la base de datos.
 *
 * Útil cuando el servidor ya tiene las tablas creadas pero la tabla
 * `migrations` de Laravel está vacía o incompleta.
 *
 * Uso:
 *   php artisan migrate:register-existing
 *   php artisan migrate:register-existing --dry-run   (solo muestra, no escribe)
 */
class RegisterExistingMigrationsCommand extends Command
{
    protected $signature = 'migrate:register-existing {--dry-run : Solo muestra las migraciones sin registrarlas}';
    protected $description = 'Registra como ejecutadas las migraciones cuyas tablas ya existen en la BD (evita el error de tabla duplicada)';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $migrationPath = database_path('migrations');

        // Si la tabla migrations aún no existe, salir — migrate --force la creará
        if (!Schema::hasTable('migrations')) {
            $this->warn('La tabla migrations no existe aún. Se creará con el migrate normal.');
            return Command::SUCCESS;
        }

        // Obtener migraciones ya registradas en la tabla migrations
        $yaRegistradas = DB::table('migrations')
            ->pluck('migration')
            ->toArray();

        $archivos = collect(scandir($migrationPath))
            ->filter(fn($f) => Str::endsWith($f, '.php'))
            ->sort()
            ->values();

        $registradas = 0;
        $saltadas = 0;
        $batch = DB::table('migrations')->max('batch') + 1;

        foreach ($archivos as $archivo) {
            $nombreMigracion = Str::replaceLast('.php', '', $archivo);

            // Si ya está registrada, saltar
            if (in_array($nombreMigracion, $yaRegistradas)) {
                $this->line("  <fg=gray>⊘ Ya registrada: {$nombreMigracion}</>");
                $saltadas++;
                continue;
            }

            // Cargar el archivo de migración y detectar la tabla que crea
            $tablaDetectada = $this->detectarTabla($migrationPath . '/' . $archivo);

            if ($tablaDetectada && $this->tablaExiste($tablaDetectada)) {
                if ($isDryRun) {
                    $this->line("  <fg=yellow>✓ [DRY-RUN] Registraría: {$nombreMigracion} (tabla: {$tablaDetectada})</>");
                } else {
                    DB::table('migrations')->insert([
                        'migration' => $nombreMigracion,
                        'batch' => $batch,
                    ]);
                    $this->line("  <fg=green>✅ Registrada: {$nombreMigracion} (tabla: {$tablaDetectada})</>");
                }
                $registradas++;
            } else {
                $this->line("  <fg=blue>→ Pendiente (tabla no existe aún): {$nombreMigracion}</>");
            }
        }

        $this->newLine();
        if ($isDryRun) {
            $this->info("DRY-RUN: {$registradas} migraciones se registrarían. {$saltadas} ya estaban registradas.");
        } else {
            $this->info("✅ {$registradas} migraciones registradas. {$saltadas} ya estaban registradas.");
            $this->info("Ahora puedes correr: php artisan migrate --force");
        }

        return Command::SUCCESS;
    }

    /**
     * Intenta detectar el nombre de la tabla que crea la migración
     * leyendo el contenido del archivo (heurística simple).
     */
    protected function detectarTabla(string $ruta): ?string
    {
        $contenido = file_get_contents($ruta);

        // Buscar Schema::create('nombre.tabla' o "nombre.tabla"
        if (preg_match('/Schema::create\([\'"]([^\'"]+)[\'"]/', $contenido, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Verifica si una tabla existe en PostgreSQL, soportando esquemas (schema.tabla).
     */
    protected function tablaExiste(string $tabla): bool
    {
        if (str_contains($tabla, '.')) {
            [$schema, $table] = explode('.', $tabla, 2);
            return (bool) DB::selectOne(
                "SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1",
                [$schema, $table]
            );
        }

        return Schema::hasTable($tabla);
    }
}

<?php

namespace Database\Factories\Auditoria;

use App\Models\Auditoria\LogActividad;
use App\Models\Auth\Usuario;
use App\Models\Core\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogActividadFactory extends Factory
{
    protected $model = LogActividad::class;

    public function definition(): array
    {
        $acciones = ['crear', 'actualizar', 'eliminar', 'ver', 'exportar', 'importar', 'login', 'logout'];
        $entidades = [
            'estudiante', 'docente', 'acudiente', 'curso', 'matricula',
            'calificacion', 'observacion', 'asistencia', 'mensaje', 'evento',
            'usuario', 'institucion', 'sede', 'asignatura', 'periodo',
        ];

        $descripciones = [
            'Se registró una nueva calificación en el sistema',
            'Se actualizó la información del estudiante',
            'Se eliminó un registro del observador',
            'Se exportó el listado de estudiantes',
            'Se importaron calificaciones desde archivo Excel',
            'Usuario inició sesión en el sistema',
            'Usuario cerró sesión',
            'Se creó una nueva matrícula',
            'Se modificó el horario de clases',
            'Se actualizaron los datos de contacto',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 14) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36',
        ];

        $accion = fake('es_ES')->randomElement($acciones);

        return [
            'usuario_id' => Usuario::factory(),
            'institucion_id' => Institucion::factory(),
            'accion' => $accion,
            'entidad' => fake('es_ES')->randomElement($entidades),
            'entidad_id' => fake('es_ES')->uuid(),
            'valores_anteriores' => in_array($accion, ['actualizar', 'eliminar']) ? $this->generarValoresAnteriores() : null,
            'valores_nuevos' => in_array($accion, ['crear', 'actualizar']) ? $this->generarValoresNuevos() : null,
            'ip' => fake('es_ES')->ipv4(),
            'user_agent' => fake('es_ES')->randomElement($userAgents),
            'descripcion' => fake('es_ES')->randomElement($descripciones),
        ];
    }

    protected function generarValoresAnteriores(): array
    {
        return [
            'nombre' => fake('es_ES')->firstName(),
            'estado' => 'activo',
            'updated_at' => fake('es_ES')->dateTimeBetween('-1 year', '-1 day')->format('Y-m-d H:i:s'),
        ];
    }

    protected function generarValoresNuevos(): array
    {
        return [
            'nombre' => fake('es_ES')->firstName(),
            'estado' => fake('es_ES')->randomElement(['activo', 'inactivo']),
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    public function crear(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion' => 'crear',
            'valores_anteriores' => null,
            'valores_nuevos' => $this->generarValoresNuevos(),
            'descripcion' => 'Se creó un nuevo registro en el sistema',
        ]);
    }

    public function actualizar(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion' => 'actualizar',
            'valores_anteriores' => $this->generarValoresAnteriores(),
            'valores_nuevos' => $this->generarValoresNuevos(),
            'descripcion' => 'Se actualizó un registro existente',
        ]);
    }

    public function eliminar(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion' => 'eliminar',
            'valores_anteriores' => $this->generarValoresAnteriores(),
            'valores_nuevos' => null,
            'descripcion' => 'Se eliminó un registro del sistema',
        ]);
    }

    public function login(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion' => 'login',
            'entidad' => 'usuario',
            'valores_anteriores' => null,
            'valores_nuevos' => null,
            'descripcion' => 'Usuario inició sesión en el sistema',
        ]);
    }

    public function logout(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion' => 'logout',
            'entidad' => 'usuario',
            'valores_anteriores' => null,
            'valores_nuevos' => null,
            'descripcion' => 'Usuario cerró sesión',
        ]);
    }

    public function exportar(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion' => 'exportar',
            'valores_anteriores' => null,
            'valores_nuevos' => ['formato' => 'xlsx', 'registros' => fake('es_ES')->numberBetween(10, 500)],
            'descripcion' => 'Se exportaron datos del sistema',
        ]);
    }
}

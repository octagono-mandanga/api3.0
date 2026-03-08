<?php

namespace Database\Factories\Notificacion;

use App\Models\Notificacion\Notificacion;
use App\Models\Notificacion\TipoNotificacion;
use App\Models\Auth\Usuario;
use App\Models\Core\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificacionFactory extends Factory
{
    protected $model = Notificacion::class;

    public function definition(): array
    {
        $titulos = [
            'Nueva calificación registrada',
            'Tienes un mensaje nuevo',
            'Se registró una observación',
            'Recordatorio de tarea pendiente',
            'Próxima evaluación programada',
            'Boletín de notas disponible',
            'Citación a reunión de padres',
            'Información importante del colegio',
            'Actualización de horarios',
            'Felicitaciones por tu desempeño',
        ];

        $contenidos = [
            'Se ha registrado una nueva calificación en Matemáticas con nota de 4.5',
            'El profesor Carlos García te ha enviado un mensaje',
            'Se registró una observación positiva por participación en clase',
            'Recuerda entregar el trabajo de Ciencias antes del viernes',
            'Tienes evaluación de Español programada para el próximo lunes',
            'El boletín del segundo período ya está disponible para descargar',
            'Ha sido citado a reunión de padres el día 15 de marzo a las 3:00 PM',
            'Les informamos sobre los cambios en el calendario académico',
            'Se han actualizado los horarios de clases para este mes',
            'Felicitaciones por obtener el primer puesto en el período',
        ];

        return [
            'usuario_id' => Usuario::factory(),
            'institucion_id' => Institucion::factory(),
            'tipo_id' => TipoNotificacion::factory(),
            'titulo' => fake('es_ES')->randomElement($titulos),
            'contenido' => fake('es_ES')->randomElement($contenidos),
            'data' => fake('es_ES')->boolean(30) ? [
                'referencia_tipo' => fake('es_ES')->randomElement(['calificacion', 'mensaje', 'observacion', 'tarea']),
                'referencia_id' => fake('es_ES')->uuid(),
            ] : null,
            'accion_url' => fake('es_ES')->boolean(50) ? '/app/' . fake('es_ES')->randomElement(['calificaciones', 'mensajes', 'observador', 'tareas']) : null,
            'leida' => fake('es_ES')->boolean(40),
            'fecha_lectura' => function (array $attributes) {
                return $attributes['leida'] ? fake('es_ES')->dateTimeBetween('-1 month', 'now') : null;
            },
            'enviada_push' => fake('es_ES')->boolean(80),
            'enviada_email' => fake('es_ES')->boolean(50),
            'fecha_expiracion' => fake('es_ES')->boolean(30) ? fake('es_ES')->dateTimeBetween('now', '+3 months') : null,
        ];
    }

    public function leida(): static
    {
        return $this->state(fn (array $attributes) => [
            'leida' => true,
            'fecha_lectura' => fake('es_ES')->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function noLeida(): static
    {
        return $this->state(fn (array $attributes) => [
            'leida' => false,
            'fecha_lectura' => null,
        ]);
    }

    public function enviadaPush(): static
    {
        return $this->state(fn (array $attributes) => [
            'enviada_push' => true,
        ]);
    }

    public function enviadaEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'enviada_email' => true,
        ]);
    }

    public function sinEnviar(): static
    {
        return $this->state(fn (array $attributes) => [
            'enviada_push' => false,
            'enviada_email' => false,
        ]);
    }

    public function expirada(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_expiracion' => fake('es_ES')->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    public function vigente(): static
    {
        return $this->state(fn (array $attributes) => [
            'fecha_expiracion' => fake('es_ES')->dateTimeBetween('+1 day', '+3 months'),
        ]);
    }

    public function conAccion(): static
    {
        return $this->state(fn (array $attributes) => [
            'accion_url' => '/app/' . fake('es_ES')->randomElement(['calificaciones', 'mensajes', 'observador', 'tareas']),
        ]);
    }
}

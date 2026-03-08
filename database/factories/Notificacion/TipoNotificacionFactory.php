<?php

namespace Database\Factories\Notificacion;

use App\Models\Notificacion\TipoNotificacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoNotificacionFactory extends Factory
{
    protected $model = TipoNotificacion::class;

    public function definition(): array
    {
        $tipos = [
            [
                'nombre' => 'Nueva calificación',
                'codigo' => 'CALIF_NUEVA',
                'categoria' => 'academico',
                'plantilla_titulo' => 'Nueva calificación registrada',
                'plantilla_cuerpo' => 'Se ha registrado una nueva calificación en {asignatura}',
            ],
            [
                'nombre' => 'Observación registrada',
                'codigo' => 'OBS_NUEVA',
                'categoria' => 'observador',
                'plantilla_titulo' => 'Nueva observación en el observador',
                'plantilla_cuerpo' => 'Se ha registrado una observación para {estudiante}',
            ],
            [
                'nombre' => 'Mensaje nuevo',
                'codigo' => 'MSG_NUEVO',
                'categoria' => 'mensajeria',
                'plantilla_titulo' => 'Tienes un nuevo mensaje',
                'plantilla_cuerpo' => '{remitente} te ha enviado un mensaje',
            ],
            [
                'nombre' => 'Inasistencia registrada',
                'codigo' => 'INAS_REG',
                'categoria' => 'asistencia',
                'plantilla_titulo' => 'Inasistencia registrada',
                'plantilla_cuerpo' => 'Se registró una inasistencia el día {fecha}',
            ],
            [
                'nombre' => 'Tarea asignada',
                'codigo' => 'TAREA_ASIG',
                'categoria' => 'academico',
                'plantilla_titulo' => 'Nueva tarea asignada',
                'plantilla_cuerpo' => 'Se ha asignado una nueva tarea en {asignatura}',
            ],
            [
                'nombre' => 'Próximo evento',
                'codigo' => 'EVENTO_PROX',
                'categoria' => 'calendario',
                'plantilla_titulo' => 'Evento próximo',
                'plantilla_cuerpo' => 'Recuerda que tienes {evento} el día {fecha}',
            ],
            [
                'nombre' => 'Boletín disponible',
                'codigo' => 'BOLETIN_DISP',
                'categoria' => 'academico',
                'plantilla_titulo' => 'Boletín de calificaciones disponible',
                'plantilla_cuerpo' => 'El boletín del período {periodo} ya está disponible',
            ],
            [
                'nombre' => 'Citación a acudiente',
                'codigo' => 'CITACION',
                'categoria' => 'institucional',
                'plantilla_titulo' => 'Citación a reunión',
                'plantilla_cuerpo' => 'Ha sido citado a reunión el día {fecha}',
            ],
        ];

        $tipo = fake('es_ES')->randomElement($tipos);
        $estados = ['activo', 'inactivo'];
        $iconos = ['bell', 'grade', 'message', 'calendar', 'warning', 'info', 'success', 'task'];

        return [
            'nombre' => $tipo['nombre'],
            'codigo' => $tipo['codigo'] . '_' . fake('es_ES')->unique()->numberBetween(1, 9999),
            'categoria' => $tipo['categoria'],
            'plantilla_titulo' => $tipo['plantilla_titulo'],
            'plantilla_cuerpo' => $tipo['plantilla_cuerpo'],
            'icono' => fake('es_ES')->randomElement($iconos),
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function academico(): static
    {
        return $this->state(fn (array $attributes) => [
            'categoria' => 'academico',
        ]);
    }

    public function mensajeria(): static
    {
        return $this->state(fn (array $attributes) => [
            'categoria' => 'mensajeria',
        ]);
    }

    public function observador(): static
    {
        return $this->state(fn (array $attributes) => [
            'categoria' => 'observador',
        ]);
    }

    public function institucional(): static
    {
        return $this->state(fn (array $attributes) => [
            'categoria' => 'institucional',
        ]);
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activo',
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}

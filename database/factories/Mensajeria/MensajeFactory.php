<?php

namespace Database\Factories\Mensajeria;

use App\Models\Mensajeria\Mensaje;
use App\Models\Mensajeria\Conversacion;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class MensajeFactory extends Factory
{
    protected $model = Mensaje::class;

    public function definition(): array
    {
        $contenidos = [
            'Buenos días, ¿cómo está el rendimiento de mi hijo?',
            'Le recuerdo que mañana hay entrega de trabajos.',
            'Por favor revisar las calificaciones del período.',
            'Necesito hablar con usted sobre el comportamiento del estudiante.',
            'Gracias por su atención y compromiso con los estudiantes.',
            '¿Cuándo son las evaluaciones finales?',
            'El estudiante no asistió a clases hoy, ¿está enfermo?',
            'Les informo que habrá reunión de padres el próximo viernes.',
            'Por favor enviar los materiales para la actividad.',
            'Felicitaciones por los excelentes resultados académicos.',
            '¿Podemos agendar una cita para hablar sobre el progreso?',
            'Adjunto el documento solicitado para el proceso.',
            'Quedo atento a cualquier novedad sobre el estudiante.',
            'Les comparto el horario de tutorías de esta semana.',
            'Recuerden que el plazo de entrega es hasta el viernes.',
        ];

        return [
            'conversacion_id' => Conversacion::factory(),
            'autor_id' => Usuario::factory(),
            'contenido' => fake('es_ES')->randomElement($contenidos),
            'adjuntos' => fake('es_ES')->boolean(20) ? $this->generarAdjuntos() : null,
            'respuesta_a' => null,
            'editado' => false,
            'fecha_edicion' => null,
            'eliminado' => false,
            'fecha_eliminacion' => null,
        ];
    }

    protected function generarAdjuntos(): array
    {
        $adjuntos = [];
        $cantidadAdjuntos = fake('es_ES')->numberBetween(1, 3);

        for ($i = 0; $i < $cantidadAdjuntos; $i++) {
            $adjuntos[] = [
                'nombre' => fake('es_ES')->word() . '.' . fake('es_ES')->randomElement(['pdf', 'docx', 'xlsx', 'jpg', 'png']),
                'url' => fake('es_ES')->url(),
                'tamaño' => fake('es_ES')->numberBetween(10000, 5000000),
                'tipo' => fake('es_ES')->mimeType(),
            ];
        }

        return $adjuntos;
    }

    public function conAdjuntos(): static
    {
        return $this->state(fn (array $attributes) => [
            'adjuntos' => $this->generarAdjuntos(),
        ]);
    }

    public function sinAdjuntos(): static
    {
        return $this->state(fn (array $attributes) => [
            'adjuntos' => null,
        ]);
    }

    public function editado(): static
    {
        return $this->state(fn (array $attributes) => [
            'editado' => true,
            'fecha_edicion' => fake('es_ES')->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function eliminado(): static
    {
        return $this->state(fn (array $attributes) => [
            'eliminado' => true,
            'fecha_eliminacion' => fake('es_ES')->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function respuestaA(string $mensajeId): static
    {
        return $this->state(fn (array $attributes) => [
            'respuesta_a' => $mensajeId,
        ]);
    }

    public function largo(): static
    {
        return $this->state(fn (array $attributes) => [
            'contenido' => fake('es_ES')->paragraphs(3, true),
        ]);
    }

    public function corto(): static
    {
        return $this->state(fn (array $attributes) => [
            'contenido' => fake('es_ES')->sentence(5),
        ]);
    }
}

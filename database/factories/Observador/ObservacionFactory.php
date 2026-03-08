<?php

namespace Database\Factories\Observador;

use App\Models\Observador\Observacion;
use App\Models\Observador\TipoObservacion;
use App\Models\Core\Institucion;
use App\Models\Inscripcion\Matricula;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ObservacionFactory extends Factory
{
    protected $model = Observacion::class;

    public function definition(): array
    {
        $descripciones = [
            'El estudiante demostró excelente participación en clase.',
            'Se felicita al estudiante por su comportamiento ejemplar.',
            'El estudiante llegó tarde a clase sin justificación.',
            'Se presentó incumplimiento en la entrega de trabajos.',
            'Excelente desempeño en la actividad grupal.',
            'Se evidencia falta de respeto hacia compañeros.',
            'El estudiante mostró mejoría significativa en su rendimiento.',
            'Se requiere acompañamiento en el área de matemáticas.',
            'Participación destacada en la izada de bandera.',
            'Incumplimiento del uniforme institucional.',
        ];

        $compromisos = [
            'El estudiante se compromete a mejorar su comportamiento.',
            'El acudiente se compromete a realizar seguimiento en casa.',
            'Se acuerda plan de mejoramiento académico.',
            'Compromiso de puntualidad para las próximas clases.',
            null,
        ];

        return [
            'institucion_id' => Institucion::factory(),
            'matricula_id' => Matricula::factory(),
            'autor_id' => Usuario::factory()->docente(),
            'tipo_id' => TipoObservacion::factory(),
            'descripcion' => fake()->randomElement($descripciones),
            'fecha' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'compromiso' => fake()->randomElement($compromisos),
            'seguimiento' => fake()->optional(0.3, fn() => fake('es_ES')->paragraph(1)),
            'notificar_acudiente' => fake()->boolean(70),
            'visto_por_acudiente' => fake()->boolean(50),
            'fecha_visto_acudiente' => fake()->optional(0.4)->dateTimeBetween('-2 months', 'now'),
            'estado' => 'activa',
        ];
    }

    public function positiva(): static
    {
        return $this->state(fn(array $attributes) => [
            'descripcion' => 'Felicitaciones por su excelente desempeño académico y comportamiento.',
            'compromiso' => null,
        ]);
    }

    public function negativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'descripcion' => 'Se presenta falta disciplinaria que requiere atención.',
            'compromiso' => 'El estudiante se compromete a mejorar su comportamiento.',
            'notificar_acudiente' => true,
        ]);
    }

    public function conSeguimiento(): static
    {
        return $this->state(fn(array $attributes) => [
            'seguimiento' => 'Se realizó seguimiento y se evidencia mejoría en el comportamiento.',
        ]);
    }

    public function vistaAcudiente(): static
    {
        return $this->state(fn(array $attributes) => [
            'notificar_acudiente' => true,
            'visto_por_acudiente' => true,
            'fecha_visto_acudiente' => now(),
        ]);
    }
}

<?php

namespace Database\Factories\Observador;

use App\Models\Observador\Asistencia;
use App\Models\Observador\TipoAusencia;
use App\Models\Core\Institucion;
use App\Models\Inscripcion\Matricula;
use App\Models\Inscripcion\Curso;
use App\Models\Academico\Asignatura;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsistenciaFactory extends Factory
{
    protected $model = Asistencia::class;

    public function definition(): array
    {
        $presente = fake()->boolean(85);

        return [
            'institucion_id' => Institucion::factory(),
            'matricula_id' => Matricula::factory(),
            'curso_id' => Curso::factory(),
            'asignatura_id' => Asignatura::factory(),
            'fecha' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'presente' => $presente,
            'tipo_ausencia_id' => $presente ? null : TipoAusencia::factory(),
            'justificada' => $presente ? null : fake()->boolean(40),
            'justificacion' => $presente ? null : fake()->optional(0.3, fn() => fake('es_ES')->sentence(6)),
            'registrado_por' => Usuario::factory()->docente(),
        ];
    }

    public function presente(): static
    {
        return $this->state(fn(array $attributes) => [
            'presente' => true,
            'tipo_ausencia_id' => null,
            'justificada' => null,
            'justificacion' => null,
        ]);
    }

    public function ausente(): static
    {
        return $this->state(fn(array $attributes) => [
            'presente' => false,
            'tipo_ausencia_id' => TipoAusencia::factory(),
            'justificada' => false,
        ]);
    }

    public function ausenteJustificado(): static
    {
        return $this->state(fn(array $attributes) => [
            'presente' => false,
            'tipo_ausencia_id' => TipoAusencia::factory()->enfermedad(),
            'justificada' => true,
            'justificacion' => 'Incapacidad médica presentada.',
        ]);
    }

    public function tardanza(): static
    {
        return $this->state(fn(array $attributes) => [
            'presente' => true,
            'justificacion' => 'Llegó con retardo de 10 minutos.',
        ]);
    }
}

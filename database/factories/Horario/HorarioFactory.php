<?php

namespace Database\Factories\Horario;

use App\Models\Horario\Horario;
use App\Models\Horario\FranjaHoraria;
use App\Models\Core\Institucion;
use App\Models\Inscripcion\Curso;
use App\Models\Inscripcion\DocenteAsignatura;
use Illuminate\Database\Eloquent\Factories\Factory;

class HorarioFactory extends Factory
{
    protected $model = Horario::class;

    public function definition(): array
    {
        $diasSemana = [1, 2, 3, 4, 5]; // Lunes a viernes
        $estados = ['activo', 'inactivo', 'suspendido'];

        $aulas = [
            'Aula 101', 'Aula 102', 'Aula 103', 'Aula 201', 'Aula 202',
            'Laboratorio de Ciencias', 'Laboratorio de Informática',
            'Sala de Audiovisuales', 'Biblioteca', 'Aula Múltiple',
            'Cancha Deportiva', 'Sala de Música', 'Taller de Arte',
        ];

        return [
            'institucion_id' => Institucion::factory(),
            'curso_id' => Curso::factory(),
            'docente_asignatura_id' => DocenteAsignatura::factory(),
            'franja_id' => FranjaHoraria::factory(),
            'dia_semana' => fake('es_ES')->randomElement($diasSemana),
            'aula' => fake('es_ES')->randomElement($aulas),
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function lunes(): static
    {
        return $this->state(fn (array $attributes) => [
            'dia_semana' => 1,
        ]);
    }

    public function martes(): static
    {
        return $this->state(fn (array $attributes) => [
            'dia_semana' => 2,
        ]);
    }

    public function miercoles(): static
    {
        return $this->state(fn (array $attributes) => [
            'dia_semana' => 3,
        ]);
    }

    public function jueves(): static
    {
        return $this->state(fn (array $attributes) => [
            'dia_semana' => 4,
        ]);
    }

    public function viernes(): static
    {
        return $this->state(fn (array $attributes) => [
            'dia_semana' => 5,
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

    public function suspendido(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'suspendido',
        ]);
    }

    public function enLaboratorio(): static
    {
        return $this->state(fn (array $attributes) => [
            'aula' => fake('es_ES')->randomElement(['Laboratorio de Ciencias', 'Laboratorio de Informática']),
        ]);
    }

    public function enAulaRegular(): static
    {
        return $this->state(fn (array $attributes) => [
            'aula' => fake('es_ES')->randomElement(['Aula 101', 'Aula 102', 'Aula 103', 'Aula 201', 'Aula 202']),
        ]);
    }
}

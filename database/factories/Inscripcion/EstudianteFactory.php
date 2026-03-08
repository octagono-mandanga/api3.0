<?php

namespace Database\Factories\Inscripcion;

use App\Models\Inscripcion\Estudiante;
use App\Models\Auth\Usuario;
use App\Models\Core\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition(): array
    {
        $gruposSanguineos = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        return [
            'usuario_id' => Usuario::factory()->menorEdad(),
            'institucion_id' => Institucion::factory(),
            'codigo_estudiante' => fake()->unique()->numerify('EST####'),
            'fecha_ingreso' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'colegio_procedencia' => fake()->optional(0.4)->company() . ' (Colegio)',
            'grupo_sanguineo' => fake()->optional(0.7)->randomElement($gruposSanguineos),
            'alergias' => fake()->optional(0.2)->randomElement([
                'Polvo', 'Polen', 'Maní', 'Mariscos', 'Ninguna conocida'
            ]),
            'medicamentos' => fake()->optional(0.1)->sentence(3),
            'condiciones_medicas' => fake()->optional(0.15)->randomElement([
                'Asma', 'Diabetes', 'Epilepsia', 'Ninguna'
            ]),
            'contacto_emergencia_nombre' => fake('es_ES')->name(),
            'contacto_emergencia_telefono' => fake()->numerify('3#########'),
            'contacto_emergencia_parentesco' => fake()->randomElement(['Madre', 'Padre', 'Tío/a', 'Abuelo/a']),
            'observaciones' => fake()->optional(0.3, fn() => fake('es_ES')->sentence(10)),
            'estado' => 'activo',
        ];
    }

    public function nuevo(): static
    {
        return $this->state(fn(array $attributes) => [
            'fecha_ingreso' => now()->format('Y-m-d'),
            'colegio_procedencia' => fake()->company() . ' (Colegio)',
            'estado' => 'activo',
        ]);
    }

    public function retirado(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'retirado',
        ]);
    }

    public function graduado(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'graduado',
        ]);
    }
}

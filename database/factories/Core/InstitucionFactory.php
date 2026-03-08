<?php

namespace Database\Factories\Core;

use App\Models\Core\Institucion;
use App\Models\Core\Plan;
use App\Models\Core\Tema;
use App\Models\Ref\Municipio;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitucionFactory extends Factory
{
    protected $model = Institucion::class;

    public function definition(): array
    {
        $tipos = ['oficial', 'privado', 'mixto'];
        $calendarios = ['A', 'B'];

        $nombreBase = fake()->randomElement([
            'Institución Educativa', 'Colegio', 'Centro Educativo', 'Liceo'
        ]);
        $nombreComplemento = fake()->randomElement([
            'San José', 'Santa María', 'La Esperanza', 'El Progreso',
            'Simón Bolívar', 'Antonio Nariño', 'Francisco de Paula Santander',
            'Jorge Eliécer Gaitán', 'Nueva Granada', 'Los Andes'
        ]);

        return [
            'plan_id' => Plan::factory(),
            'tema_id' => Tema::factory(),
            'nombre' => "{$nombreBase} {$nombreComplemento}",
            'nombre_corto' => fake()->lexify('IE???'),
            'codigo_dane' => fake()->unique()->numerify('###############'),
            'nit' => fake()->unique()->numerify('#########-#'),
            'tipo' => fake()->randomElement($tipos),
            'calendario' => fake()->randomElement($calendarios),
            'email' => fake()->unique()->companyEmail(),
            'telefono' => fake()->numerify('#######'),
            'celular' => fake()->numerify('3#########'),
            'direccion' => fake('es_ES')->streetAddress(),
            'municipio_id' => Municipio::factory(),
            'sitio_web' => fake()->optional(0.5)->url(),
            'logo_url' => null,
            'rector_nombre' => fake('es_ES')->name(),
            'resolucion_aprobacion' => fake()->optional(0.7)->numerify('Res. ### de ####'),
            'fecha_fundacion' => fake()->optional(0.6)->dateTimeBetween('-80 years', '-5 years'),
            'configuracion' => [
                'permitir_registro_online' => fake()->boolean(30),
                'enviar_notificaciones_email' => true,
                'enviar_notificaciones_push' => true,
            ],
            'estado' => 'activo',
        ];
    }

    public function oficial(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'oficial',
        ]);
    }

    public function privado(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => 'privado',
        ]);
    }

    public function calendarioA(): static
    {
        return $this->state(fn(array $attributes) => [
            'calendario' => 'A',
        ]);
    }

    public function calendarioB(): static
    {
        return $this->state(fn(array $attributes) => [
            'calendario' => 'B',
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }
}

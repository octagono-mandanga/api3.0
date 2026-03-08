<?php

namespace Database\Factories;

use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Auth\Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    /**
     * Contraseña actual utilizada por la fábrica.
     */
    protected static ?string $password;

    /**
     * Define el estado predeterminado del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $generos = ['M', 'F', 'O'];

        return [
            'tipo_documento_id' => fake()->numberBetween(1, 5),
            'numero_documento' => fake()->unique()->numerify('##########'),
            'primer_nombre' => fake('es_ES')->firstName(),
            'segundo_nombre' => fake()->optional(0.5)->firstName(),
            'primer_apellido' => fake('es_ES')->lastName(),
            'segundo_apellido' => fake('es_ES')->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verificado_en' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'telefono' => fake()->optional(0.3)->numerify('#######'),
            'celular' => fake()->numerify('3#########'),
            'direccion' => fake('es_ES')->streetAddress(),
            'fecha_nacimiento' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'genero' => fake()->randomElement($generos),
            'municipio_id' => fake()->optional(0.8)->numberBetween(1, 100),
            'etnia_id' => fake()->optional(0.2)->numberBetween(1, 10),
            'discapacidad_id' => fake()->optional(0.1)->numberBetween(1, 10),
            'eps_id' => fake()->optional(0.7)->numberBetween(1, 20),
            'foto_url' => null,
            'estado' => 'activo',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indica que el email del modelo no debe estar verificado.
     */
    public function sinVerificar(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verificado_en' => null,
        ]);
    }

    /**
     * Indica que el usuario está inactivo.
     */
    public function inactivo(): static
    {
        return $this->state(fn(array $attributes) => [
            'estado' => 'inactivo',
        ]);
    }

    /**
     * Indica que el usuario es menor de edad (estudiante).
     */
    public function menorEdad(): static
    {
        return $this->state(fn(array $attributes) => [
            'fecha_nacimiento' => fake()->dateTimeBetween('-17 years', '-5 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Indica que el usuario es docente (adulto).
     */
    public function docente(): static
    {
        return $this->state(fn(array $attributes) => [
            'fecha_nacimiento' => fake()->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
        ]);
    }
}

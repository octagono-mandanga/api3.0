<?php

namespace Database\Factories\Core;

use App\Models\Core\Tema;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemaFactory extends Factory
{
    protected $model = Tema::class;

    public function definition(): array
    {
        $temas = [
            ['nombre' => 'Clásico', 'codigo' => 'clasico', 'primario' => '#1976D2', 'secundario' => '#424242'],
            ['nombre' => 'Moderno', 'codigo' => 'moderno', 'primario' => '#00BCD4', 'secundario' => '#FF5722'],
            ['nombre' => 'Natural', 'codigo' => 'natural', 'primario' => '#4CAF50', 'secundario' => '#8D6E63'],
            ['nombre' => 'Elegante', 'codigo' => 'elegante', 'primario' => '#673AB7', 'secundario' => '#FFC107'],
            ['nombre' => 'Institucional', 'codigo' => 'institucional', 'primario' => '#0D47A1', 'secundario' => '#B71C1C'],
        ];

        $tema = fake()->randomElement($temas);

        return [
            'nombre' => $tema['nombre'],
            'codigo' => $tema['codigo'],
            'color_primario' => $tema['primario'],
            'color_secundario' => $tema['secundario'],
            'color_acento' => fake()->hexColor(),
            'fuente_principal' => fake()->randomElement(['Roboto', 'Open Sans', 'Lato', 'Montserrat']),
            'fuente_secundaria' => fake()->randomElement(['Roboto', 'Open Sans', 'Lato', 'Montserrat']),
            'variables_css' => [],
            'estado' => true,
        ];
    }
}

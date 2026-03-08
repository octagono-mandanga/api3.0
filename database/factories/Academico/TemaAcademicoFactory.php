<?php

namespace Database\Factories\Academico;

use App\Models\Academico\TemaAcademico;
use App\Models\Academico\UnidadTematica;
use Illuminate\Database\Eloquent\Factories\Factory;

class TemaAcademicoFactory extends Factory
{
    protected $model = TemaAcademico::class;

    public function definition(): array
    {
        $temas = [
            'Suma y resta de números naturales',
            'Multiplicación y división',
            'Fracciones equivalentes',
            'Ecuaciones lineales',
            'Polígonos y sus propiedades',
            'Perímetro y área',
            'El texto narrativo',
            'El texto argumentativo',
            'Ortografía: uso de tildes',
            'El verbo y sus tiempos',
            'La célula animal y vegetal',
            'El sistema digestivo',
            'El ciclo del agua',
            'Regiones naturales de Colombia',
            'La independencia de Colombia',
            'Los derechos humanos',
            'Fundamentos del baloncesto',
            'Técnicas de dibujo a lápiz',
        ];

        return [
            'unidad_id' => UnidadTematica::factory(),
            'nombre' => fake()->randomElement($temas),
            'descripcion' => fake('es_ES')->paragraph(1),
            'recursos' => fake()->optional(0.5)->randomElements([
                'Video explicativo',
                'Presentación PowerPoint',
                'Guía de trabajo',
                'Material didáctico',
                'Ejercicios prácticos',
            ], fake()->numberBetween(1, 3)),
            'duracion_horas' => fake()->numberBetween(1, 4),
            'orden' => fake()->numberBetween(1, 6),
            'estado' => true,
        ];
    }
}

<?php

namespace Database\Factories\Academico;

use App\Models\Academico\UnidadTematica;
use App\Models\Academico\AsignaturaGrado;
use App\Models\Evaluacion\Periodo;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadTematicaFactory extends Factory
{
    protected $model = UnidadTematica::class;

    public function definition(): array
    {
        $unidades = [
            'Números y Operaciones',
            'Geometría y Medición',
            'Estadística y Probabilidad',
            'Álgebra y Funciones',
            'Comprensión Lectora',
            'Producción Textual',
            'Gramática y Ortografía',
            'Literatura Colombiana',
            'Ecosistemas y Medio Ambiente',
            'El Cuerpo Humano',
            'Historia de Colombia',
            'Geografía Mundial',
            'Deportes Individuales',
            'Deportes Colectivos',
            'Dibujo y Pintura',
            'Música y Expresión',
        ];

        return [
            'asignatura_grado_id' => AsignaturaGrado::factory(),
            'periodo_id' => Periodo::factory(),
            'nombre' => fake()->randomElement($unidades),
            'descripcion' => fake('es_ES')->paragraph(2),
            'objetivos' => fake('es_ES')->paragraph(3),
            'orden' => fake()->numberBetween(1, 4),
            'estado' => true,
        ];
    }
}

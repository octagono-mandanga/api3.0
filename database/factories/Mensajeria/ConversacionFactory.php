<?php

namespace Database\Factories\Mensajeria;

use App\Models\Mensajeria\Conversacion;
use App\Models\Core\Institucion;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversacionFactory extends Factory
{
    protected $model = Conversacion::class;

    public function definition(): array
    {
        $tipos = ['individual', 'grupal', 'canal'];
        $estados = ['activa', 'archivada', 'cerrada'];
        $contextos = ['curso', 'asignatura', 'general', 'soporte'];

        $asuntos = [
            'Consulta sobre tareas pendientes',
            'Recordatorio de entrega de trabajos',
            'Información sobre reunión de padres',
            'Dudas sobre el examen final',
            'Solicitud de permiso especial',
            'Seguimiento académico del estudiante',
            'Comunicación importante del colegio',
            'Felicitaciones por logros académicos',
            'Recordatorio de pago de pensión',
            'Información sobre salida pedagógica',
            'Consulta sobre calificaciones',
            'Apoyo emocional al estudiante',
            'Coordinación de actividades extracurriculares',
            'Notificación de citación a acudiente',
            'Información sobre eventos escolares',
        ];

        return [
            'institucion_id' => Institucion::factory(),
            'asunto' => fake('es_ES')->randomElement($asuntos),
            'tipo' => fake('es_ES')->randomElement($tipos),
            'contexto_tipo' => fake('es_ES')->randomElement($contextos),
            'contexto_id' => fake('es_ES')->uuid(),
            'creador_id' => Usuario::factory(),
            'estado' => fake('es_ES')->randomElement($estados),
        ];
    }

    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'individual',
        ]);
    }

    public function grupal(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'grupal',
        ]);
    }

    public function canal(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'canal',
        ]);
    }

    public function activa(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'activa',
        ]);
    }

    public function archivada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'archivada',
        ]);
    }

    public function cerrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'cerrada',
        ]);
    }

    public function contextoCurso(string $cursoId): static
    {
        return $this->state(fn (array $attributes) => [
            'contexto_tipo' => 'curso',
            'contexto_id' => $cursoId,
        ]);
    }

    public function contextoAsignatura(string $asignaturaId): static
    {
        return $this->state(fn (array $attributes) => [
            'contexto_tipo' => 'asignatura',
            'contexto_id' => $asignaturaId,
        ]);
    }
}

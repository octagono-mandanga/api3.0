<?php

namespace Database\Factories\Notificacion;

use App\Models\Notificacion\Preferencia;
use App\Models\Notificacion\TipoNotificacion;
use App\Models\Auth\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreferenciaFactory extends Factory
{
    protected $model = Preferencia::class;

    public function definition(): array
    {
        return [
            'usuario_id' => Usuario::factory(),
            'tipo_notificacion_id' => TipoNotificacion::factory(),
            'canal_push' => fake('es_ES')->boolean(80),
            'canal_email' => fake('es_ES')->boolean(60),
            'canal_sms' => fake('es_ES')->boolean(20),
        ];
    }

    public function todosCanales(): static
    {
        return $this->state(fn (array $attributes) => [
            'canal_push' => true,
            'canal_email' => true,
            'canal_sms' => true,
        ]);
    }

    public function ningunCanal(): static
    {
        return $this->state(fn (array $attributes) => [
            'canal_push' => false,
            'canal_email' => false,
            'canal_sms' => false,
        ]);
    }

    public function soloPush(): static
    {
        return $this->state(fn (array $attributes) => [
            'canal_push' => true,
            'canal_email' => false,
            'canal_sms' => false,
        ]);
    }

    public function soloEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'canal_push' => false,
            'canal_email' => true,
            'canal_sms' => false,
        ]);
    }

    public function soloSms(): static
    {
        return $this->state(fn (array $attributes) => [
            'canal_push' => false,
            'canal_email' => false,
            'canal_sms' => true,
        ]);
    }

    public function pushYEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'canal_push' => true,
            'canal_email' => true,
            'canal_sms' => false,
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla para almacenar las solicitudes de activación institucional.
     */
    public function up(): void
    {
        Schema::create('core.solicitudes_activacion', function (Blueprint $table) {
            $table->id();

            // Datos de la institución
            $table->string('nombre_institucion');
            $table->string('correo');
            $table->string('telefono', 20);

            // Datos del responsable
            $table->string('nombre_responsable');
            $table->string('documento', 20);

            // Códigos de verificación
            $table->string('codigo_email', 6)->nullable();
            $table->string('codigo_sms', 4)->nullable();
            $table->boolean('email_verificado')->default(false);
            $table->boolean('sms_verificado')->default(false);

            // Estado del proceso
            $table->enum('estado', [
                'pendiente_email',
                'pendiente_sms',
                'completada',
                'cancelada',
                'expirada'
            ])->default('pendiente_email');

            // Tracking de seguridad
            $table->ipAddress('ip_origen')->nullable();
            $table->text('user_agent')->nullable();

            // Referencia a institución creada (una vez completada)
            $table->unsignedBigInteger('institucion_id')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('correo');
            $table->index('estado');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core.solicitudes_activacion');
    }
};

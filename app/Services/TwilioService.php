<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected Client $client;
    protected string $fromNumber;
    protected string $brandName;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $this->client = new Client($sid, $token);
        $this->fromNumber = config('services.twilio.from');
        $this->brandName = config('services.twilio.brand_name', 'Octagono');
    }

    /**
     * Envía un código de verificación por SMS.
     */
    public function enviarCodigoVerificacion(string $telefono, string $codigo): bool
    {
        try {
            // Formatear número (asegurar formato internacional)
            $phoneNumber = $this->formatearNumero($telefono);

            $mensaje = "Tu código de verificación de {$this->brandName} es: {$codigo}\n\nEste código expira en 10 minutos. No lo compartas con nadie.";

            $this->client->messages->create(
                $phoneNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $mensaje,
                ]
            );

            Log::info('SMS de verificación enviado', [
                'telefono' => $this->enmascararTelefono($telefono),
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al enviar SMS de verificación', [
                'telefono' => $this->enmascararTelefono($telefono),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Formatea el número de teléfono al formato internacional.
     */
    protected function formatearNumero(string $telefono): string
    {
        // Eliminar espacios y caracteres no numéricos excepto +
        $telefono = preg_replace('/[^\d+]/', '', $telefono);

        // Si no tiene código de país, asumir Colombia (+57)
        if (!str_starts_with($telefono, '+')) {
            // Si empieza con 57, agregar +
            if (str_starts_with($telefono, '57')) {
                $telefono = '+' . $telefono;
            }
            // Si es un número de 10 dígitos (móvil colombiano sin código)
            elseif (strlen($telefono) === 10 && str_starts_with($telefono, '3')) {
                $telefono = '+57' . $telefono;
            }
            // Otro caso, agregar +57
            else {
                $telefono = '+57' . $telefono;
            }
        }

        return $telefono;
    }

    /**
     * Enmascara el número de teléfono para logs (privacidad).
     */
    protected function enmascararTelefono(string $telefono): string
    {
        $len = strlen($telefono);
        if ($len <= 4) {
            return '****';
        }
        return substr($telefono, 0, 3) . str_repeat('*', $len - 5) . substr($telefono, -2);
    }
}

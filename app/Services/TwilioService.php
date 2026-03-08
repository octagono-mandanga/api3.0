<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected ?Client $client = null;
    protected string $fromNumber;
    protected string $brandName;
    protected bool $habilitado;

    public function __construct()
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->fromNumber = config('services.twilio.from', '');
        $this->brandName  = config('services.twilio.brand_name', 'Octagono');

        // Si las credenciales no están configuradas, deshabilitar silenciosamente
        // en lugar de explotar el constructor (lo que rompería también el email).
        if (empty($sid) || empty($token) || empty($this->fromNumber)) {
            $this->habilitado = false;
            Log::warning('TwilioService: credenciales no configuradas (TWILIO_SID, TWILIO_TOKEN, TWILIO_FROM).');
            return;
        }

        try {
            $this->client    = new Client($sid, $token);
            $this->habilitado = true;
        } catch (\Exception $e) {
            $this->habilitado = false;
            Log::error('TwilioService: error al inicializar cliente Twilio', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Envía un código de verificación por SMS.
     */
    public function enviarCodigoVerificacion(string $telefono, string $codigo): bool
    {
        if (!$this->habilitado || $this->client === null) {
            Log::warning('TwilioService: SMS no enviado — servicio deshabilitado o sin credenciales.', [
                'telefono' => $this->enmascararTelefono($telefono),
            ]);
            return false;
        }

        try {
            $phoneNumber = $this->formatearNumero($telefono);

            // Twilio no permite enviar al mismo número de origen (falla con HTTP 400)
            if ($phoneNumber === $this->fromNumber) {
                Log::error('TwilioService: el número destino es igual al número origen (TWILIO_FROM).', [
                    'telefono' => $this->enmascararTelefono($telefono),
                ]);
                return false;
            }

            $mensaje = "Tu código de verificación de {$this->brandName} es: {$codigo}\n\nExpira en 10 minutos. No lo compartas con nadie.";

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

        } catch (\Twilio\Exceptions\RestException $e) {
            // Errores conocidos de la API de Twilio con diagnóstico claro
            $codigo_error = $e->getStatusCode();
            $detalle = match (true) {
                str_contains($e->getMessage(), 'not a verified')
                    => 'Cuenta Twilio en modo Trial: el número destino no está verificado ir a twilio.com/console/phone-numbers/verified',
                str_contains($e->getMessage(), 'cannot be the same')
                    => 'El número destino es igual al número Twilio FROM — usa un número diferente para la prueba',
                str_contains($e->getMessage(), 'not a valid phone number')
                    => 'Número de teléfono con formato inválido: ' . $telefono,
                str_contains($e->getMessage(), 'Account is not active')
                    => 'La cuenta Twilio no está activa o está suspendida',
                default
                    => $e->getMessage(),
            };

            Log::error('TwilioService: error de API al enviar SMS', [
                'telefono' => $this->enmascararTelefono($telefono),
                'codigo_http' => $codigo_error,
                'diagnostico' => $detalle,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('TwilioService: error inesperado al enviar SMS', [
                'telefono' => $this->enmascararTelefono($telefono),
                'error'    => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Verifica si el servicio está habilitado y configurado.
     */
    public function estaHabilitado(): bool
    {
        return $this->habilitado;
    }

    /**
     * Formatea el número de teléfono al formato internacional E.164.
     */
    protected function formatearNumero(string $telefono): string
    {
        // Eliminar espacios y caracteres no numéricos excepto +
        $telefono = preg_replace('/[^\d+]/', '', $telefono);

        if (!str_starts_with($telefono, '+')) {
            if (str_starts_with($telefono, '57') && strlen($telefono) === 12) {
                // Ya tiene código de país sin el +
                $telefono = '+' . $telefono;
            } elseif (strlen($telefono) === 10 && str_starts_with($telefono, '3')) {
                // Móvil colombiano de 10 dígitos (3XX XXX XXXX)
                $telefono = '+57' . $telefono;
            } else {
                // Fallback: agregar +57
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

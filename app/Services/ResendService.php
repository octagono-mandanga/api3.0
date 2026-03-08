<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResendService
{
    protected string $fromEmail;
    protected string $fromName;

    public function __construct()
    {
        $this->fromEmail = config('services.resend.from_email', 'verificacion@octagono.app');
        $this->fromName = config('services.resend.from_name', 'Octágono');
    }

    /**
     * Envía un código de verificación por email.
     * Usa el mailer configurado en MAIL_MAILER (log en dev, resend en prod).
     */
    public function enviarCodigoVerificacion(string $correo, string $codigo, string $nombre): bool
    {
        try {
            $html = $this->getEmailTemplate($codigo, $nombre);

            Mail::html($html, function ($message) use ($correo) {
                $message->from($this->fromEmail, $this->fromName)
                    ->to($correo)
                    ->subject("Código de verificación - {$this->fromName}");
            });

            Log::info('Email de verificación enviado', [
                'correo' => $correo,
                'nombre' => $nombre,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al enviar email de verificación', [
                'correo' => $correo,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Template HTML del email de verificación.
     */
    protected function getEmailTemplate(string $codigo, string $nombre): string
    {
        $logoUrl = config('app.url') . '/images/logo.png';
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Verificación</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">Octágono</h1>
                            <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">Sistema de Gestión Educativa</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 16px 0; color: #374151; font-size: 16px;">
                                Hola <strong>{$nombre}</strong>,
                            </p>
                            <p style="margin: 0 0 32px 0; color: #6b7280; font-size: 15px; line-height: 1.6;">
                                Recibimos una solicitud para verificar tu correo electrónico. Usa el siguiente código para completar el proceso:
                            </p>

                            <!-- Code Box -->
                            <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid #0ea5e9; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 32px;">
                                <p style="margin: 0 0 8px 0; color: #0369a1; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Tu código de verificación</p>
                                <p style="margin: 0; color: #0c4a6e; font-size: 36px; font-weight: 700; letter-spacing: 8px; font-family: 'Courier New', monospace;">{$codigo}</p>
                            </div>

                            <!-- Warning -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 0 8px 8px 0; margin-bottom: 24px;">
                                <p style="margin: 0; color: #92400e; font-size: 14px;">
                                    <strong>⏱ Este código expira en 10 minutos.</strong><br>
                                    Si no solicitaste este código, puedes ignorar este mensaje de forma segura.
                                </p>
                            </div>

                            <p style="margin: 0; color: #9ca3af; font-size: 13px; text-align: center;">
                                Por seguridad, nunca compartas este código con nadie.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                                © {$year} Octágono - Sistema de Gestión Educativa<br>
                                Este es un mensaje automático, por favor no respondas a este correo.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}

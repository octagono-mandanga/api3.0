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
     * Envía un email de notificación cuando la cuenta ha sido creada exitosamente
     * mediante el proceso de registro realizado por un facilitador/implementador.
     */
    public function enviarNotificacionCuentaCreada(
        string $correo,
        string $nombreInstitucion,
        string $nombreResponsable,
        string $urlConfiguracion = '',
        string $urlPlataforma = 'https://app.octagono.co'
    ): bool {
        try {
            $html = $this->getEmailCuentaCreadaTemplate($nombreInstitucion, $nombreResponsable, $urlConfiguracion, $urlPlataforma);

            Mail::html($html, function ($message) use ($correo, $nombreInstitucion) {
                $message->from($this->fromEmail, $this->fromName)
                    ->to($correo)
                    ->subject("✅ Cuenta creada — {$nombreInstitucion} ya está en Octágono");
            });

            Log::info('Email de cuenta creada enviado', [
                'correo'            => $correo,
                'nombre_institucion' => $nombreInstitucion,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al enviar email de cuenta creada', [
                'correo' => $correo,
                'error'  => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Envía las credenciales de acceso al administrador institucional recién creado.
     * Se llama al final del wizard de configuración inicial.
     */
    public function enviarBienvenidaAdministrador(
        string $correo,
        string $nombreCompleto,
        string $nombreInstitucion,
        string $passwordTemporal,
        string $urlClienteWeb
    ): bool {
        try {
            $html = $this->getEmailBienvenidaAdminTemplate(
                $nombreCompleto,
                $nombreInstitucion,
                $correo,
                $passwordTemporal,
                $urlClienteWeb
            );

            Mail::html($html, function ($message) use ($correo, $nombreInstitucion) {
                $message->from($this->fromEmail, $this->fromName)
                    ->to($correo)
                    ->subject("🎓 Bienvenido a Octágono — {$nombreInstitucion}");
            });

            Log::info('Email de bienvenida administrador enviado', [
                'correo'            => $correo,
                'nombre_institucion' => $nombreInstitucion,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al enviar email de bienvenida administrador', [
                'correo' => $correo,
                'error'  => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Notifica a un usuario EXISTENTE que ha sido asignado como administrador de una institución.
     * A diferencia de enviarBienvenidaAdministrador, NO envía contraseña porque el usuario ya existe.
     */
    public function enviarNotificacionAsignacionAdmin(
        string $correo,
        string $nombreCompleto,
        string $nombreInstitucion,
        string $urlClienteWeb
    ): bool {
        try {
            $html = $this->getEmailAsignacionAdminTemplate(
                $nombreCompleto,
                $nombreInstitucion,
                $urlClienteWeb
            );

            Mail::html($html, function ($message) use ($correo, $nombreInstitucion) {
                $message->from($this->fromEmail, $this->fromName)
                    ->to($correo)
                    ->subject("🎓 Nueva asignación — Administrador de {$nombreInstitucion}");
            });

            Log::info('Email de asignación administrador enviado', [
                'correo'             => $correo,
                'nombre_institucion' => $nombreInstitucion,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Error al enviar email de asignación administrador', [
                'correo' => $correo,
                'error'  => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Template HTML para notificar a usuario existente sobre asignación como admin.
     */
    protected function getEmailAsignacionAdminTemplate(
        string $nombreCompleto,
        string $nombreInstitucion,
        string $urlClienteWeb
    ): string {
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva asignación — Octágono</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="width: 100%; max-width: 620px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 36px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 30px; font-weight: 700;">Octágono</h1>
                            <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">Sistema de Gestión Educativa</p>
                        </td>
                    </tr>

                    <!-- Success banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%); padding: 20px 40px; text-align: center; border-bottom: 2px solid #a78bfa;">
                            <p style="margin: 0; color: #5b21b6; font-size: 18px; font-weight: 700;">
                                🎉 ¡Has sido asignado como Administrador!
                            </p>
                        </td>
                    </tr>

                    <!-- Main content -->
                    <tr>
                        <td style="padding: 40px 40px 24px 40px;">
                            <p style="margin: 0 0 16px 0; color: #374151; font-size: 16px;">
                                Hola <strong>{$nombreCompleto}</strong>,
                            </p>
                            <p style="margin: 0 0 28px 0; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                Te informamos que has sido designado como <strong style="color: #6366f1;">Administrador Institucional</strong>
                                de <strong style="color: #1f2937;">{$nombreInstitucion}</strong> en la plataforma Octágono.
                            </p>

                            <!-- Info box -->
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px solid #93c5fd; border-radius: 12px; padding: 24px 28px; margin-bottom: 28px;">
                                <p style="margin: 0 0 12px 0; color: #1e40af; font-size: 14px; font-weight: 700;">
                                    📋 Como administrador podrás:
                                </p>
                                <ul style="margin: 0; padding-left: 20px; color: #4b5563; font-size: 14px; line-height: 1.8;">
                                    <li>Gestionar usuarios y roles de la institución</li>
                                    <li>Configurar períodos académicos y calificaciones</li>
                                    <li>Administrar la estructura organizativa</li>
                                    <li>Generar reportes institucionales</li>
                                </ul>
                            </div>

                            <!-- Access button -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <a href="{$urlClienteWeb}"
                                   style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 10px; font-size: 16px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(99,102,241,0.4);">
                                    Ingresar a la plataforma →
                                </a>
                                <p style="margin: 12px 0 0 0; color: #9ca3af; font-size: 12px;">
                                    Usa tus credenciales habituales para acceder
                                </p>
                            </div>

                            <!-- Note -->
                            <div style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 16px 20px;">
                                <p style="margin: 0; color: #0369a1; font-size: 13px; line-height: 1.6;">
                                    <strong>ℹ️ Nota:</strong> Si no reconoces esta institución o crees que esto es un error,
                                    por favor contacta al equipo de soporte de Octágono.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                                © {$year} Octágono — Sistema de Gestión Educativa<br>
                                Este es un mensaje automático generado por la plataforma.
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

    /**
     * Template HTML del email de bienvenida al administrador institucional.
     * Incluye sus credenciales de acceso y el link al cliente web.
     */
    protected function getEmailBienvenidaAdminTemplate(
        string $nombreCompleto,
        string $nombreInstitucion,
        string $correo,
        string $passwordTemporal,
        string $urlClienteWeb
    ): string {
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a Octágono</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="width: 100%; max-width: 620px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 36px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 30px; font-weight: 700;">Octágono</h1>
                            <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">Sistema de Gestión Educativa</p>
                        </td>
                    </tr>

                    <!-- Welcome banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 20px 40px; text-align: center; border-bottom: 2px solid #6ee7b7;">
                            <p style="margin: 0; color: #065f46; font-size: 18px; font-weight: 700;">
                                🎓 ¡Tu cuenta de administrador está lista!
                            </p>
                        </td>
                    </tr>

                    <!-- Main content -->
                    <tr>
                        <td style="padding: 40px 40px 24px 40px;">
                            <p style="margin: 0 0 16px 0; color: #374151; font-size: 16px;">
                                Hola <strong>{$nombreCompleto}</strong>,
                            </p>
                            <p style="margin: 0 0 28px 0; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                La configuración inicial de <strong style="color: #1f2937;">{$nombreInstitucion}</strong> en Octágono
                                ha sido completada exitosamente. A continuación encontrará sus credenciales de acceso
                                a la plataforma de gestión institucional.
                            </p>

                            <!-- Credentials box -->
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px solid #93c5fd; border-radius: 12px; padding: 24px 28px; margin-bottom: 28px;">
                                <p style="margin: 0 0 16px 0; color: #1e40af; font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    🔐 Sus credenciales de acceso
                                </p>
                                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 8px 0; color: #4b5563; font-size: 14px; width: 90px; font-weight: 600;">Usuario:</td>
                                        <td style="padding: 8px 0;">
                                            <span style="background: #ffffff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 5px 12px; font-family: monospace; font-size: 14px; color: #1e40af; font-weight: 600;">{$correo}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #4b5563; font-size: 14px; font-weight: 600;">Contraseña:</td>
                                        <td style="padding: 8px 0;">
                                            <span style="background: #ffffff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 5px 12px; font-family: monospace; font-size: 16px; color: #1e40af; font-weight: 700; letter-spacing: 2px;">{$passwordTemporal}</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Warning -->
                            <div style="background: #fef3c7; border-left: 5px solid #f59e0b; border-radius: 0 10px 10px 0; padding: 16px 20px; margin-bottom: 28px;">
                                <p style="margin: 0; color: #92400e; font-size: 14px; line-height: 1.6;">
                                    <strong>⚠️ Importante:</strong> Esta es una contraseña temporal. Por seguridad,
                                    le recomendamos cambiarla en su primer inicio de sesión en la plataforma.
                                </p>
                            </div>

                            <!-- Access button -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <a href="{$urlClienteWeb}"
                                   style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 10px; font-size: 16px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(99,102,241,0.4);">
                                    Ingresar a la plataforma →
                                </a>
                                <p style="margin: 12px 0 0 0; color: #9ca3af; font-size: 12px;">
                                    URL de acceso: <span style="color: #4f46e5;">{$urlClienteWeb}</span>
                                </p>
                            </div>

                            <!-- Info box -->
                            <div style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 16px 20px;">
                                <p style="margin: 0; color: #0369a1; font-size: 13px; line-height: 1.6;">
                                    <strong>ℹ️ Nota:</strong> Si tiene problemas para acceder o necesita asistencia,
                                    comuníquese con el equipo de soporte de Octágono.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                                © {$year} Octágono — Sistema de Gestión Educativa<br>
                                Este es un mensaje automático generado por la plataforma.
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

    /**
     * Template HTML del email de notificación de cuenta creada.
     */
    protected function getEmailCuentaCreadaTemplate(
        string $nombreInstitucion,
        string $nombreResponsable,
        string $urlConfiguracion,
        string $urlPlataforma
    ): string {
        $year = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuenta creada — Octágono</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 0;">
                <table role="presentation" style="width: 100%; max-width: 620px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); padding: 36px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 30px; font-weight: 700;">Octágono</h1>
                            <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">Sistema de Gestión Educativa</p>
                        </td>
                    </tr>

                    <!-- Success banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); padding: 20px 40px; text-align: center; border-bottom: 2px solid #6ee7b7;">
                            <p style="margin: 0; color: #065f46; font-size: 18px; font-weight: 700;">
                                ✅ ¡Cuenta institucional creada exitosamente!
                            </p>
                        </td>
                    </tr>

                    <!-- Main content -->
                    <tr>
                        <td style="padding: 40px 40px 24px 40px;">
                            <p style="margin: 0 0 16px 0; color: #374151; font-size: 16px;">
                                Estimado(a) <strong>{$nombreResponsable}</strong>,
                            </p>
                            <p style="margin: 0 0 24px 0; color: #6b7280; font-size: 15px; line-height: 1.7;">
                                La cuenta de la institución <strong style="color: #1f2937;">{$nombreInstitucion}</strong> ha sido
                                registrada en Octágono. El proceso fue realizado por el
                                <strong style="color: #4f46e5;">rector o representante legal</strong>, quien completó el formulario
                                de activación institucional.
                            </p>

                            <!-- Important notice -->
                            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-left: 5px solid #f59e0b; border-radius: 0 10px 10px 0; padding: 20px 20px 20px 20px; margin-bottom: 28px;">
                                <p style="margin: 0 0 8px 0; color: #92400e; font-size: 15px; font-weight: 700;">
                                    🎓 Próximo paso importante
                                </p>
                                <p style="margin: 0; color: #78350f; font-size: 14px; line-height: 1.7;">
                                    Es momento de que una <strong>persona con conocimientos curriculares y administrativos</strong>
                                    —o en su defecto un facilitador o implementador delegado por la institución—
                                    asuma la administración de la plataforma. Esta persona será responsable de configurar
                                    la estructura académica, los niveles educativos, los periodos y demás parámetros
                                    institucionales.
                                </p>
                            </div>

                            <!-- What they should do -->
                            <p style="margin: 0 0 16px 0; color: #374151; font-size: 15px; font-weight: 600;">
                                ¿Qué debe hacer el administrador institucional?
                            </p>
                            <table role="presentation" style="width: 100%; border-collapse: collapse; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top; width: 34px;">
                                        <div style="width: 26px; height: 26px; background: #4f46e5; border-radius: 50%; text-align: center; line-height: 26px; color: #fff; font-size: 13px; font-weight: 700;">1</div>
                                    </td>
                                    <td style="padding: 10px 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                        Acceder a la plataforma a través del <strong>enlace de configuración inicial</strong> que encontrará más abajo en este correo.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top;">
                                        <div style="width: 26px; height: 26px; background: #4f46e5; border-radius: 50%; text-align: center; line-height: 26px; color: #fff; font-size: 13px; font-weight: 700;">2</div>
                                    </td>
                                    <td style="padding: 10px 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                        Completar la configuración inicial: estructura académica, grados, áreas, periodos y escala de calificación.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; vertical-align: top;">
                                        <div style="width: 26px; height: 26px; background: #4f46e5; border-radius: 50%; text-align: center; line-height: 26px; color: #fff; font-size: 13px; font-weight: 700;">3</div>
                                    </td>
                                    <td style="padding: 10px 0; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                        Subir la documentación requerida (certificado de existencia y documento del representante legal) en un plazo de <strong>10 días</strong>.
                                    </td>
                                </tr>
                            </table>

                            <!-- Access button -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <a href="{$urlConfiguracion}"
                                   style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 10px; font-size: 16px; font-weight: 700; letter-spacing: 0.3px; box-shadow: 0 4px 12px rgba(99,102,241,0.4);">
                                    Ir a la configuración inicial →
                                </a>
                                <p style="margin: 12px 0 0 0; color: #9ca3af; font-size: 12px;">
                                    URL de configuración: <span style="color: #4f46e5;">{$urlConfiguracion}</span>
                                </p>
                                <p style="margin: 8px 0 0 0; color: #9ca3af; font-size: 12px;">
                                    Acceso general a la plataforma: <span style="color: #4f46e5;">{$urlPlataforma}</span>
                                </p>
                            </div>

                            <!-- Info box -->
                            <div style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 16px 20px;">
                                <p style="margin: 0; color: #0369a1; font-size: 13px; line-height: 1.6;">
                                    <strong>ℹ️ Nota:</strong> Este proceso fue iniciado por un facilitador externo o implementador de Octágono
                                    y no por el representante legal directamente. Si tiene alguna duda sobre el proceso de activación,
                                    comuníquese con su facilitador o con el equipo de soporte de Octágono.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 24px 40px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                                © {$year} Octágono — Sistema de Gestión Educativa<br>
                                Este es un mensaje automático generado por la plataforma.
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

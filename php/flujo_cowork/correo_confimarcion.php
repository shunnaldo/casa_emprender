<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; // Ajusta la ruta según tu proyecto

function enviarCorreoConfirmacion($correo, $nombre, $apellido, $rut, $cowork, $fecha, $hora_inicio, $hora_fin) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'mail.fomentolaflorida.cl';
        $mail->SMTPAuth = true;
        $mail->Username = 'casaemprender2025@fomentolaflorida.cl';
        $mail->Password = 'TvS9nSQmp4nJT7Q';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->SMTPDebug = 0;

        // Configuración del correo
        $mail->setFrom('casaemprender2025@fomentolaflorida.cl', 'Reservas Cowork');
        $mail->addAddress($correo, "$nombre $apellido");

        $mail->isHTML(true);
        $mail->Subject = 'Confirmacion de Reserva';
$mail->Body = '
    <div style="font-family: Arial, sans-serif; color: #333;">
        <div style="max-width: 600px; margin: auto; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;">
            <div style="text-align: center; background-color: #0a4b78; color: white; padding: 15px;">
                <h2 style="margin: 0;">Confirmación de Reserva</h2>
            </div>
            <div style="padding: 20px; background-color: #ffffff;">
                <p>Estimado/a <strong>' . htmlspecialchars($nombre) . ' ' . htmlspecialchars($apellido) . '</strong>,</p>
                <p>Tu reserva ha sido registrada con éxito. Detalles:</p>
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>RUT:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($rut) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Cowork:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($cowork) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Fecha:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">' . htmlspecialchars($fecha) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;"><strong>Horario:</strong></td>
                        <td style="padding: 8px;">' . htmlspecialchars($hora_inicio) . ' - ' . htmlspecialchars($hora_fin) . '</td>
                    </tr>
                </table>

                <p style="margin-top: 25px; text-align: center;">
                    Si deseas cancelar tu hora, haz clic en el siguiente botón:
                </p>
                <div style="text-align: center; margin: 20px 0;">
                    <a href="https://tusistema.cl/cancelar-reserva.php"
                       style="background-color: #d9534f; color: #fff; padding: 12px 20px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">
                       Cancelar Reserva
                    </a>
                </div>

                <p style="margin-top: 30px;">Saludos cordiales,<br><strong>Equipo Cofodep – Fomento La Florida</strong></p>
            </div>
            <div style="background-color: #f0f0f0; text-align: center; padding: 15px;">
                <img src="https://teatrolaflorida.cl/wp-content/uploads/2024/10/COFODEP-1024x194.png" alt="Logo Cowork" style="max-height: 40px; margin-bottom: 10px;"><br>
                <span style="font-size: 12px; color: #666;">Este correo fue enviado automáticamente. No respondas a este mensaje.</span>
            </div>
        </div>
    </div>';


        return $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
?>

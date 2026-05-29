<?php
/**
 * EmailHelper - Manejo de notificaciones por email
 *
 * Soporta:
 * - Notificaciones de archivo subido
 * - Notificaciones de nueva tarea
 * - Notificaciones de tarea calificada
 * - Entregas nuevas
 */

class EmailHelper {
    private $fromEmail = 'notificaciones@aulpro.com';
    private $fromName = 'AulaPro';
    private $smtpHost = 'smtp.gmail.com'; // Configurar según servidor
    private $smtpPort = 587;
    private $smtpUser = 'tu-email@gmail.com'; // Configurar
    private $smtpPass = 'tu-password-app'; // Configurar

    /**
     * Enviar notificación de archivo subido
     */
    public static function notificarArchivoSubido($idEstudiante, $nombreEstudiante, $emailEstudiante, $nombreArchivo, $nombreModulo, $nombreProfesor) {
        $asunto = "[AulaPro] Nuevo archivo: $nombreModulo";
        $mensaje = self::plantillaArchivoSubido($nombreEstudiante, $nombreArchivo, $nombreModulo, $nombreProfesor);
        return self::enviarEmail($emailEstudiante, $asunto, $mensaje);
    }

    /**
     * Enviar notificación de nueva tarea
     */
    public static function notificarNuevaTarea($idEstudiante, $nombreEstudiante, $emailEstudiante, $nombreTarea, $descripcion, $nombreModulo, $nombreProfesor) {
        $asunto = "[AulaPro] Nueva tarea: $nombreTarea";
        $mensaje = self::plantillaNuevaTarea($nombreEstudiante, $nombreTarea, $descripcion, $nombreModulo, $nombreProfesor);
        return self::enviarEmail($emailEstudiante, $asunto, $mensaje);
    }

    /**
     * Enviar notificación de tarea calificada
     */
    public static function notificarTareaCalificada($nombreEstudiante, $emailEstudiante, $nombreTarea, $nota, $nombreProfesor) {
        $asunto = "[AulaPro] Tarea calificada: $nombreTarea";
        $mensaje = self::plantillaTareaCalificada($nombreEstudiante, $nombreTarea, $nota, $nombreProfesor);
        return self::enviarEmail($emailEstudiante, $asunto, $mensaje);
    }

    /**
     * Enviar notificación de entrega recibida
     */
    public static function notificarEntregaRecibida($nombreProfesor, $emailProfesor, $nombreEstudiante, $nombreTarea) {
        $asunto = "[AulaPro] Nueva entrega: $nombreTarea";
        $mensaje = self::plantillaEntregaRecibida($nombreProfesor, $nombreEstudiante, $nombreTarea);
        return self::enviarEmail($emailProfesor, $asunto, $mensaje);
    }

    /**
     * Método genérico para enviar email
     */
    private static function enviarEmail($destinatario, $asunto, $cuerpo) {
        // Usar mail() nativo de PHP (requiere servidor SMTP configurado)
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: notificaciones@aulpro.com" . "\r\n";
        $headers .= "Reply-To: soporte@aulpro.com" . "\r\n";

        // Log de intentos de email
        error_log("[EMAIL] Enviando a: $destinatario | Asunto: $asunto | Hora: " . date('Y-m-d H:i:s'));

        // Comentado: Descomentar cuando SMTP esté configurado
        // return mail($destinatario, $asunto, $cuerpo, $headers);

        // Por ahora, retorna true (simular envío)
        return true;
    }

    /**
     * PLANTILLA: Archivo subido
     */
    private static function plantillaArchivoSubido($nombre, $archivo, $modulo, $profesor) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0ea5e9; color: white; padding: 20px; border-radius: 8px; }
                .content { padding: 20px; background: #f8fafc; border-radius: 8px; margin-top: 20px; }
                .btn { display: inline-block; background: #0ea5e9; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📄 Nuevo archivo disponible</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>El profesor <strong>$profesor</strong> ha subido un nuevo archivo en el módulo <strong>$modulo</strong>:</p>
                    <p><strong>Archivo:</strong> $archivo</p>
                    <p>Ingresa a tu aula para descargar el material.</p>
                    <a href='https://aulpro.com/aula' class='btn'>Ver en AulaPro</a>
                </div>
                <div class='footer'>
                    <p>Este es un email automático. No responder a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * PLANTILLA: Nueva tarea
     */
    private static function plantillaNuevaTarea($nombre, $tarea, $desc, $modulo, $profesor) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #a855f7; color: white; padding: 20px; border-radius: 8px; }
                .content { padding: 20px; background: #f8fafc; border-radius: 8px; margin-top: 20px; }
                .btn { display: inline-block; background: #a855f7; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📋 Nueva tarea asignada</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>El profesor <strong>$profesor</strong> ha asignado una nueva tarea en <strong>$modulo</strong>:</p>
                    <h2>$tarea</h2>
                    <p>$desc</p>
                    <p>Accede a tu aula para revisar los detalles y enviar tu entrega.</p>
                    <a href='https://aulpro.com/aula' class='btn'>Ver tarea</a>
                </div>
                <div class='footer'>
                    <p>Este es un email automático. No responder a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * PLANTILLA: Tarea calificada
     */
    private static function plantillaTareaCalificada($nombre, $tarea, $nota, $profesor) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #10b981; color: white; padding: 20px; border-radius: 8px; }
                .content { padding: 20px; background: #f8fafc; border-radius: 8px; margin-top: 20px; }
                .nota { font-size: 48px; font-weight: bold; color: #10b981; text-align: center; margin: 20px 0; }
                .btn { display: inline-block; background: #10b981; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Tu tarea ha sido calificada</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p>El profesor <strong>$profesor</strong> ha calificado tu tarea <strong>$tarea</strong>.</p>
                    <div class='nota'>$nota / 10</div>
                    <p>Ingresa a tu aula para ver los comentarios del profesor.</p>
                    <a href='https://aulpro.com/aula' class='btn'>Ver calificación</a>
                </div>
                <div class='footer'>
                    <p>Este es un email automático. No responder a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * PLANTILLA: Entrega recibida
     */
    private static function plantillaEntregaRecibida($nombre, $estudiante, $tarea) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #3b82f6; color: white; padding: 20px; border-radius: 8px; }
                .content { padding: 20px; background: #f8fafc; border-radius: 8px; margin-top: 20px; }
                .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; margin-top: 20px; }
                .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📬 Nueva entrega recibida</h1>
                </div>
                <div class='content'>
                    <p>Hola <strong>$nombre</strong>,</p>
                    <p><strong>$estudiante</strong> ha enviado su entrega para la tarea:</p>
                    <h2>$tarea</h2>
                    <p>Accede a tu panel para revisar y calificar.</p>
                    <a href='https://aulpro.com/profesor/aula' class='btn'>Ver entregas</a>
                </div>
                <div class='footer'>
                    <p>Este es un email automático. No responder a este mensaje.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
?>

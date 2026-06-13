
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
 
$mail = new PHPMailer(true);
$post_email = $_POST['email'];

try {
    #$mail->SMTPDebug = 2;  // Sacar esta línea para no mostrar salida debug
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';  // Host de conexión SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'admisiones@calasanz.eus';                 // Usuario SMTP
    $mail->Password = 'PlataformaMatriculas21';                           // Password SMTP
    $mail->SMTPSecure = 'tls';                            // Activar seguridad TLS
    $mail->Port = 587;                                    // Puerto SMTP

    #$mail->SMTPOptions = ['ssl'=> ['allow_self_signed' => true]];  // Descomentar si el servidor SMTP tiene un certificado autofirmado
    #$mail->SMTPSecure = false;				// Descomentar si se requiere desactivar cifrado (se suele usar en conjunto con la siguiente línea)
    #$mail->SMTPAutoTLS = false;			// Descomentar si se requiere desactivar completamente TLS (sin cifrado)
 
    $mail->setFrom('admisiones@calasanz.eus','Admisiones Calasanz Santurtzi');		// Mail del remitente
    $mail->addAddress($post_email);     // Mail del destinatario
 
    $mail->isHTML(true);
    $mail->Subject = 'Te damos la bienvenida a Calasanz Santurtzi';  // Asunto del mensaje
    $mail->Body    = utf8_decode("<p>Buenos días,<p/><p>En primer lugar, queremos darte la bienvenida a Calasanz Santurtzi. Esperamos que tu primera toma de contacto con el centro haya sido positiva.<p/><p>En segundo lugar, queremos aprovechar este mensaje para comprobar que la dirección de correo electrónico que tenemos es válida y no presenta erratas. No es necesario que contestes el mensaje, puesto que ya recibimos una notificación en el caso de que sea correcto.<p/>Además, aprovechamos para comentarte que hemos publicado en nuestra <a href='https://calasanz.eus/' target='_blank'>página web</a> el listado de libros de texto necesarios para los diferentes ciclos, así como el <a href='https://calasanz.eus/1_informacion_inicio_curso_primeros/' target='_blank'>calendario de inicio de curso</a> y demás información relevante al calendario de inicio de curso. Los libros podrán adquirirse directamente a través de nuestra <a href='https://calasanz.eus/libros-de-texto-fp/' target='_blank'>tienda online</a>. Recuerda que solo es necesaria la compra de aquellos productos en los que se indica '1º curso'.<p/><p>Por otro lado, te enviamos el documento resumen con la información general, requisitos informáticos mínimos con los que deberán contar los ordenadores para el inicio del curso y condiciones económicas. Puedes ver el <a href='https://calasanz.eus/guia-fp/' target='_blank'>documento desde aquí</a>.</p>Un saludo,<p/>");    // Contenido del mensaje (acepta HTML)

    $mail->AltBody = utf8_decode("Buenos días. En primer lugar, queremos darte la bienvenida a Calasanz Santurtzi. Esperamos que tu primera toma de contacto con el centro haya sido positiva. En segundo lugar, queremos aprovechar este mensaje para comprobar que la dirección de correo electrónico que tenemos es válida y no presenta erratas. No es necesario que contestes el mensaje, puesto que ya recibimos una notificación en el caso de que sea correcto. Además, aprovechamos para comentarte que hemos publicado en nuestra página web (https://calasanz.eus) el listado de libros de texto necesarios para los diferentes ciclos, así como el calendario de inicio de curso y demás información relevante. Los libros podrán adquirirse directamente a través de nuestra tienda online (https://calasanz.eus/libros-de-texto-fp/). Recuerda que solo es necesaria la compra de aquellos productos en los que se indica '1º curso'. Por otro lado, te enviamos el documento resumen con la información general, requisitos informáticos mínimos con los que deberán contar los ordenadores para el inicio del curso y condiciones económicas. (https://calasanz.eus/guia-fp/)</p>Un saludo,<p/>Un saludo.");    // Contenido del mensaje alternativo (texto plano)
 
    $mail->send();
    header('Location: ../panel_gestion.php?status=success_email');
} catch (Exception $e) {
    header('Location: ../panel_gestion.php?status=error_email');
}
<?php
// cron_recordatorios.php
// Se debe ejecutar una vez al día mediante un cron job (ej: 0 8 * * *)
require_once __DIR__ . '/../modelos/conectar.php';

try {
    $db = obtenerConexion();
    
    // Buscar pagos que vencen en exactamente 3 días
    $sql = "
        SELECT p.idPago, p.fechaProximoPago, p.monto, e.idEstudiante, e.nombreEstudiante, e.emailEstudiante
        FROM pagos p
        JOIN estudiantes e ON p.idEstudiante = e.idEstudiante
        WHERE p.fechaProximoPago = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
          AND e.eliminado = 0
    ";
    
    $res = mysqli_query($db, $sql);
    $pagosProximos = [];
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $pagosProximos[] = $row;
        }
    }
    
    if (count($pagosProximos) > 0) {
        $sqlInsert = "INSERT INTO cola_emails (destinatario_email, destinatario_nombre, asunto, html_content) VALUES (?, ?, ?, ?)";
        $stmtInsert = mysqli_prepare($db, $sqlInsert);
        
        foreach ($pagosProximos as $p) {
            $fechaFmt = date('d/m/Y', strtotime($p['fechaProximoPago']));
            $cuerpo = "Hola {$p['nombreEstudiante']},\n\nTe recordamos que tu próximo pago de {$p['monto']}€ vence en 3 días (el {$fechaFmt}).\n\nPor favor, ingresa a tu portal para regularizar tu situación y evitar bloqueos en el Aula Digital.\n\nSaludos,\nSecretaría.";
            
            $asunto = 'Recordatorio de Pago Próximo - AulaPro';
            $cuerpoHtml = nl2br($cuerpo);
            
            mysqli_stmt_bind_param($stmtInsert, "ssss", $p['emailEstudiante'], $p['nombreEstudiante'], $asunto, $cuerpoHtml);
            mysqli_stmt_execute($stmtInsert);

            echo "Recordatorio encolado para: {$p['emailEstudiante']}\n";

            // Copia a las familias vinculadas: son quienes gestionan los pagos desde su portal
            $sqlTutores = "SELECT t.nombreTutor, t.emailTutor
                           FROM tutores t
                           JOIN estudiante_tutor et ON et.idTutor = t.idTutor
                           WHERE et.idEstudiante = ? AND t.emailTutor IS NOT NULL AND t.emailTutor != ''";
            $stmtTutores = mysqli_prepare($db, $sqlTutores);
            mysqli_stmt_bind_param($stmtTutores, "i", $p['idEstudiante']);
            mysqli_stmt_execute($stmtTutores);
            $resTutores = mysqli_stmt_get_result($stmtTutores);
            while ($tut = mysqli_fetch_assoc($resTutores)) {
                $cuerpoTutor = "Hola {$tut['nombreTutor']},\n\nLe recordamos que el próximo pago de {$p['monto']}€ correspondiente a {$p['nombreEstudiante']} vence en 3 días (el {$fechaFmt}).\n\nPuede consultar el estado de los pagos desde el Portal de Familias.\n\nSaludos,\nSecretaría.";
                $cuerpoTutorHtml = nl2br($cuerpoTutor);
                mysqli_stmt_bind_param($stmtInsert, "ssss", $tut['emailTutor'], $tut['nombreTutor'], $asunto, $cuerpoTutorHtml);
                mysqli_stmt_execute($stmtInsert);
                echo "Recordatorio (familia) encolado para: {$tut['emailTutor']}\n";
            }
        }
    } else {
        echo "No hay pagos próximos a 3 días hoy.\n";
    }
    
    echo "Proceso finalizado correctamente.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

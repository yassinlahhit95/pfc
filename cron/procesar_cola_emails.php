<?php
// ══════════════════════════════════════════════════════════════════════
// CRON: Procesador de cola de emails
// Ejecutar cada minuto: * * * * * php /path/to/cron/procesar_cola_emails.php
// ══════════════════════════════════════════════════════════════════════

// Guard: only from CLI or server-local calls.
if (PHP_SAPI !== 'cli' && ($_SERVER['REMOTE_ADDR'] ?? '') !== '127.0.0.1') {
    http_response_code(403);
    exit('Forbidden');
}

define('RUNNING_AS_CRON', true);

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/../modelos/cola_emails.php';
require_once __DIR__ . '/../controladores/comunes/email_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$con = obtenerConexion();
$pendientes = obtenerEmailsPendientes(10);

$enviados = 0;
$fallidos = 0;
$errorMsg = NULL;
$status = 'success';

if (empty($pendientes)) {
    $errorMsg = 'No hay correos pendientes';
} else {
    foreach ($pendientes as $item) {
        $ok = sendEmail(
            $item['destinatario_email'],
            $item['asunto'],
            $item['html_content'],
            'CFP - AulaPro | Sistema Académico'
        );

        if ($ok) {
            marcarEmailEnviado((int)$item['id']);
            $enviados++;
        } else {
            $error = $_SESSION['ultimo_error_email'] ?? 'Error desconocido';
            marcarEmailFallido((int)$item['id'], $error);
            $fallidos++;
        }
    }

    if ($fallidos > 0) {
        $status = 'failed';
        $errorMsg = "Enviados: $enviados, Fallidos: $fallidos";
    } else {
        $errorMsg = "Procesados: $enviados correos";
    }
}

// Log cron execution to database
if ($con) {
    $sql = "INSERT INTO cron_execution_log (job_name, last_run, last_run_status, error_message)
            VALUES ('procesar_cola_emails.php', NOW(), ?, ?)
            ON DUPLICATE KEY UPDATE last_run = NOW(), last_run_status = ?, error_message = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $status, $errorMsg, $status, $errorMsg);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

echo "Cola procesada: $enviados enviados, $fallidos fallidos.\n";

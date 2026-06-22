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

$pendientes = obtenerEmailsPendientes(10);

if (empty($pendientes)) {
    exit(0);
}

$enviados = 0;
$fallidos = 0;

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

echo "Cola procesada: $enviados enviados, $fallidos fallidos.\n";

<?php
// GET /controladores/comunes/recordatorios/procesar.php?token=CRON_TOKEN
// Pensado para invocarse periódicamente (cron) — sin guard de sesión, solo
// autenticación por token compartido (CRON_TOKEN en .env). Genera las
// notificaciones de los recordatorios de eventos cuyo momento ya llegó.
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . "/../../../modelos/notificacionesRecordatorios.php";

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// Config::getInstance() parsea .env (y hace putenv de cada clave) la primera
// vez que se instancia — forzarlo aquí para que getenv() ya vea CRON_TOKEN,
// en vez de depender de que algo más lo haya instanciado antes por casualidad.
Config::getInstance();
$tokenEsperado = getenv('CRON_TOKEN');
$tokenRecibido = $_GET['token'] ?? '';

if (empty($tokenEsperado) || !is_string($tokenRecibido) || !hash_equals((string)$tokenEsperado, $tokenRecibido)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Token inválido.']);
    exit;
}

try {
    $resultado = procesarRecordatoriosPendientes();
    echo json_encode([
        'ok'                     => true,
        'timestamp'              => date('c'),
        'procesados'             => (int)($resultado['procesados'] ?? 0),
        'notificaciones_creadas' => (int)($resultado['creados'] ?? 0),
    ]);
} catch (\Throwable $e) {
    error_log('Error en procesarRecordatoriosPendientes (cron): ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Error al procesar recordatorios.']);
}

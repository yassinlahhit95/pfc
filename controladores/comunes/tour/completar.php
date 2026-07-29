<?php
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../include/Security.php';
require_once __DIR__ . '/../../../include/MfaService.php';
// Marca un tour de onboarding como completado (terminado o saltado) para el
// usuario actual, cualquiera que sea su rol — llamado por
// public/js/core/onboarding-tour.js al terminar o pulsar "Saltar".
Security::initSession();

header('Content-Type: application/json; charset=utf-8');

$actor = MfaService::sesionActual();
if (!$actor) { http_response_code(403); echo json_encode(['ok' => false]); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken($_POST['csrf_token'] ?? null, false)) {
    http_response_code(400);
    echo json_encode(['ok' => false]);
    exit;
}

require_once __DIR__ . '/../../../modelos/tours.php';

$tourKey = trim($_POST['tourKey'] ?? '');
if ($tourKey === '' || strlen($tourKey) > 50) {
    echo json_encode(['ok' => false]);
    exit;
}

$tipoUsuario = lcfirst(substr($actor['sessionKey'], 2)); // 'idProfesor' -> 'profesor'
$ok = marcarTourCompletado((int)$actor['id'], $tipoUsuario, $tourKey);

echo json_encode(['ok' => $ok]);

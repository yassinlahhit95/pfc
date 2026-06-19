<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/Security.php';
header('Content-Type: application/json');

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (empty($_SESSION['idAdmin']) || !empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'Acceso denegado.']); exit;
}

require_once __DIR__ . '/../../../modelos/horarios.php';

if (!Security::validateCSRFToken()) {
    echo json_encode(['ok' => false, 'msg' => 'CSRF inválido']); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idCiclo = (int)($_POST['idCiclo']   ?? 0);
$inicio  = trim($_POST['horaInicio'] ?? '');

if (!$idCiclo || !$inicio) {
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']); exit;
}

// El formato en BD es HH:MM:00
$horaSql = $inicio . ':00';

if (tieneCeldasEnFranja($idCiclo, $horaSql)) {
    echo json_encode(['ok' => false, 'msg' => 'Elimina primero todos los módulos asignados en esta franja']); exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$ok = eliminarFranjaHorario($idCiclo, $horaSql);
echo json_encode(['ok' => (bool)$ok]);

<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../modelos/horarios.php';

// rotate=false — see addFranja.php for why (shared token, no-reload page).
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit;
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
if ($ok) registrarAccion('remove_franja', 'horario', $idCiclo, $inicio);
echo json_encode(['ok' => (bool)$ok]);

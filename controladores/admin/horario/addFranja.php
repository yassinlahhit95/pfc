<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../modelos/horarios.php';

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
// rotate=false — horario.php is a single-page drag-and-drop UI that never
// reloads between actions, reusing one shared CSRF token for the whole
// session (see public/js/features/horario.js). The default rotate=true
// would delete that token after the first successful call, breaking every
// action after it with "Token de seguridad inválido" (same bug class
// documented in CLAUDE.md for the landing builder).
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit;
}

$idCiclo  = (int)($_POST['idCiclo']   ?? 0);
$inicio   = trim($_POST['horaInicio'] ?? '');
$fin      = trim($_POST['horaFin']    ?? '');
$esReceso = !empty($_POST['esReceso']);

// Formato HH:MM
if (!$idCiclo
    || !preg_match('/^\d{2}:\d{2}$/', $inicio)
    || !preg_match('/^\d{2}:\d{2}$/', $fin)
) {
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']); exit;
}

// Rango permitido: 08:00 ≤ inicio < fin ≤ 21:00
if ($inicio < '08:00' || $fin > '21:00' || $inicio >= $fin) {
    echo json_encode(['ok' => false, 'msg' => 'Horario permitido: 08:00 a 21:00, fin debe ser posterior al inicio']); exit;
}

// Duración máxima: 60 minutos
$toMin = function($t) { return (int)substr($t,0,2)*60 + (int)substr($t,3,2); };
if ($toMin($fin) - $toMin($inicio) > 60) {
    echo json_encode(['ok' => false, 'msg' => 'Una franja no puede durar más de 1 hora']); exit;
}

// Conflicto: la nueva franja se solapa con otra ya existente para este ciclo
// (no basta con comparar solo la hora de inicio: una franja 09:30-10:30 se
// solaparía con una 09:00-10:00 existente sin coincidir en el inicio).
$franjasCiclo = obtenerFranjasHorario($idCiclo);
foreach ($franjasCiclo as $franja) {
    if ($inicio < $franja['fin'] && $franja['inicio'] < $fin) {
        echo json_encode(['ok' => false, 'msg' => 'Esa franja horaria se solapa con otra ya existente para este ciclo']); exit;
    }
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$ok = agregarFranjaHorario($idCiclo, $inicio, $fin, $esReceso);
if ($ok) registrarAccion('add_franja', 'horario', $idCiclo, "$inicio-$fin");
echo json_encode(['ok' => (bool)$ok]);

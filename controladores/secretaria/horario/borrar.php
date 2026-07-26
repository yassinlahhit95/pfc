<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../../../modelos/horarios.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida.']);
    exit;
}

// rotate=false — shared token reused across this no-reload drag-and-drop page (see controladores/admin/horario/addFranja.php)
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idCiclo    = (int)($_POST['idCiclo'] ?? 0);
$dia        = trim($_POST['dia'] ?? '');
$horaInicio = trim($_POST['horaInicio'] ?? '');

$diasValidos = obtenerDiasHorario();
if ($idCiclo <= 0 || !in_array($dia, $diasValidos, true) || $horaInicio === '') {
    echo json_encode(['ok' => false, 'msg' => 'Datos de celda no válidos.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (borrarCeldaHorario($idCiclo, $dia, $horaInicio . ':00')) {
    registrarAccionSecretaria('borrar', 'horario', $idCiclo, "$dia $horaInicio");
    echo json_encode(['ok' => true, 'msg' => 'Asignación eliminada.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo eliminar la asignación.']);
}
exit;

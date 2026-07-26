<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../../../modelos/horarios.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida.']);
    exit;
}

// rotate=false — see addFranja.php for why (shared token, no-reload page).
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idCiclo    = (int)($_POST['idCiclo'] ?? 0);
$dia        = trim($_POST['dia'] ?? '');
$horaInicio = trim($_POST['horaInicio'] ?? '');
$idModulo   = (int)($_POST['idModulo'] ?? 0);
$idProfesor = (int)($_POST['idProfesor'] ?? 0);
$idAula     = (int)($_POST['idAula'] ?? 0);
$idAula     = $idAula > 0 ? $idAula : null;   // 0 / vacío = sin aula

// Validar contra los valores permitidos (dia y franja reales)
$diasValidos    = obtenerDiasHorario();
$franjasValidas = [];
foreach (obtenerFranjasHorario($idCiclo) as $f) {
    if (empty($f['recreo'])) $franjasValidas[$f['inicio']] = $f['fin'];
}

if ($idCiclo <= 0 || !in_array($dia, $diasValidos, true) || !isset($franjasValidas[$horaInicio])) {
    echo json_encode(['ok' => false, 'msg' => 'Datos de celda no válidos.']);
    exit;
}
if ($idModulo <= 0 || $idProfesor <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Falta el módulo o el profesor.']);
    exit;
}

// Forzar la hora fin correcta de la franja (no fiarse del cliente)
$horaFin = $franjasValidas[$horaInicio];
$horaSql = $horaInicio . ':00';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
// Comprobación de conflictos previa al INSERT (las claves UNIQUE de la BD son la red de seguridad final)
$confProf = profesorOcupadoPorOtro($idProfesor, $dia, $horaSql, $idCiclo);
if ($confProf) {
    echo json_encode(['ok' => false, 'msg' => 'El profesor ya imparte a esa hora en ' .
        $confProf['abreviaturaCiclo'] . ' (' . ($confProf['nombreModulo'] ?? 'otro módulo') . ').']);
    exit;
}
$confAula = aulaOcupadaPorOtro($idAula, $dia, $horaSql, $idCiclo);
if ($confAula) {
    echo json_encode(['ok' => false, 'msg' => 'Esa aula ya está ocupada a esa hora por ' .
        $confAula['abreviaturaCiclo'] . ' (' . ($confAula['nombreModulo'] ?? 'otro módulo') . ').']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (guardarCeldaHorario($idCiclo, $dia, $horaSql, $horaFin . ':00', $idModulo, $idProfesor, $idAula)) {
    registrarAccion('guardar', 'horario', $idCiclo, "$dia $horaInicio");
    echo json_encode(['ok' => true, 'msg' => 'Asignación guardada.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar (posible solapamiento de aula o profesor).']);
}
exit;

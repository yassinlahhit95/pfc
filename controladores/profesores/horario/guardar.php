<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../../modelos/horarios.php';

if (empty($_SESSION['esTutor']) || empty($_SESSION['idCicloTutor'])) {
    http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'Acceso denegado.']); exit;
}
$idCicloTutor = (int)$_SESSION['idCicloTutor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida.']); exit;
}

// rotate=false — shared token reused across this no-reload drag-and-drop page (see controladores/admin/horario/addFranja.php)
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit;
}

$idCiclo    = (int)($_POST['idCiclo'] ?? 0);
$dia        = trim($_POST['dia'] ?? '');
$horaInicio = trim($_POST['horaInicio'] ?? '');
$idModulo   = (int)($_POST['idModulo'] ?? 0);
$idProfesor = (int)($_POST['idProfesor'] ?? 0);
$idAula     = (int)($_POST['idAula'] ?? 0);
$idAula     = $idAula > 0 ? $idAula : null;

if ($idCiclo !== $idCicloTutor) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para editar este ciclo.']); exit;
}

$diasValidos    = obtenerDiasHorario();
$franjasValidas = [];
foreach (obtenerFranjasHorario($idCiclo) as $f) {
    if (empty($f['recreo'])) $franjasValidas[$f['inicio']] = $f['fin'];
}

if ($idCiclo <= 0 || !in_array($dia, $diasValidos, true) || !isset($franjasValidas[$horaInicio])) {
    echo json_encode(['ok' => false, 'msg' => 'Datos de celda no válidos.']); exit;
}
if ($idModulo <= 0 || $idProfesor <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Falta el módulo o el profesor.']); exit;
}

$horaFin = $franjasValidas[$horaInicio];
$horaSql = $horaInicio . ':00';

$confProf = profesorOcupadoPorOtro($idProfesor, $dia, $horaSql, $idCiclo);
if ($confProf) {
    echo json_encode(['ok' => false, 'msg' => 'El profesor ya imparte a esa hora en ' .
        $confProf['abreviaturaCiclo'] . ' (' . ($confProf['nombreModulo'] ?? 'otro módulo') . ').']); exit;
}
$confAula = aulaOcupadaPorOtro($idAula, $dia, $horaSql, $idCiclo);
if ($confAula) {
    echo json_encode(['ok' => false, 'msg' => 'Esa aula ya está ocupada a esa hora por ' .
        $confAula['abreviaturaCiclo'] . ' (' . ($confAula['nombreModulo'] ?? 'otro módulo') . ').']); exit;
}

if (guardarCeldaHorario($idCiclo, $dia, $horaSql, $horaFin . ':00', $idModulo, $idProfesor, $idAula)) {
    echo json_encode(['ok' => true, 'msg' => 'Asignación guardada.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar (posible solapamiento de aula o profesor).']);
}
exit;

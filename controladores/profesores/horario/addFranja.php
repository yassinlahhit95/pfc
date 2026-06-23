<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
header('Content-Type: application/json');
require_once __DIR__ . '/../../../modelos/horarios.php';

if (empty($_SESSION['esTutor']) || empty($_SESSION['idCicloTutor'])) {
    http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'Acceso denegado.']); exit;
}
$idCicloTutor = (int)$_SESSION['idCicloTutor'];

$idCiclo  = (int)($_POST['idCiclo']   ?? 0);
$inicio   = trim($_POST['horaInicio'] ?? '');
$fin      = trim($_POST['horaFin']    ?? '');
$esReceso = !empty($_POST['esReceso']);

if ($idCiclo !== $idCicloTutor) {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para editar este ciclo.']); exit;
}

if (!$idCiclo || !preg_match('/^\d{2}:\d{2}$/', $inicio) || !preg_match('/^\d{2}:\d{2}$/', $fin)) {
    echo json_encode(['ok' => false, 'msg' => 'Datos inválidos']); exit;
}

if ($inicio < '08:00' || $fin > '21:00' || $inicio >= $fin) {
    echo json_encode(['ok' => false, 'msg' => 'Horario permitido: 08:00 a 21:00, fin debe ser posterior al inicio']); exit;
}

$toMin = function ($t) { return (int)substr($t, 0, 2) * 60 + (int)substr($t, 3, 2); };
if ($toMin($fin) - $toMin($inicio) > 60) {
    echo json_encode(['ok' => false, 'msg' => 'Una franja no puede durar más de 1 hora']); exit;
}

$usedStarts = array_column(obtenerFranjasHorario($idCiclo), 'inicio');
if (in_array($inicio, $usedStarts)) {
    echo json_encode(['ok' => false, 'msg' => 'Esa hora de inicio ya está en uso para este ciclo']); exit;
}

$ok = agregarFranjaHorario($idCiclo, $inicio, $fin, $esReceso);
echo json_encode(['ok' => (bool)$ok]);

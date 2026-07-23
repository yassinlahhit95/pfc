<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function asistencias_salir($ok, $msg, $idModulo, $fecha, $isAjax) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'msg' => $msg]);
        exit;
    }
    if ($ok) { $_SESSION['exito'] = $msg; } else { $_SESSION['errores'] = $msg; }
    header("Location: ../../../vistas/profesores/asistencias/registrar.php?idModulo=$idModulo&fecha=$fecha");
    exit;
}

if (!Security::validateCSRFToken()) {
    asistencias_salir(false, 'Solicitud inválida. Inténtelo de nuevo.', (int)($_POST['idModulo'] ?? 0), $_POST['fecha'] ?? '', $isAjax);
}

$idProfesor   = (int)$_SESSION['idProfesor'];
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$idModulo     = (int)($_POST['idModulo'] ?? 0);
$fecha        = $_POST['fecha'] ?? '';

// IDOR: verify module access
if ($idModulo && $esTutor && $idCicloTutor) {
    if (!moduloPerteneceACiclo($idModulo, $idCicloTutor)) {
        asistencias_salir(false, "No tienes permiso sobre este módulo.", $idModulo, $fecha, $isAjax);
    }
}

if (!$idModulo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    asistencias_salir(false, "Datos no válidos.", $idModulo, $fecha, $isAjax);
}

$registros = [];
foreach ($_POST['registros'] ?? [] as $idEst => $r) {
    $idEst = (int)$idEst;
    if (!$idEst) continue;
    $estado = $r['estado'] ?? 'presente';
    if (!in_array($estado, ['presente','ausente','retraso','justificado'], true)) $estado = 'presente';
    $obs    = trim($r['observacion'] ?? '') ?: null;
    $registros[] = ['idEstudiante' => $idEst, 'estado' => $estado, 'observacion' => $obs];
}

if (empty($registros)) {
    asistencias_salir(false, "No se recibieron registros.", $idModulo, $fecha, $isAjax);
}

if (guardarAsistenciasMasivo($idModulo, $idProfesor, $fecha, $registros)) {
    registrarAccion('registrar_asistencia', 'asistencias', $idModulo, "Módulo #$idModulo · $fecha · " . count($registros) . " alumnos");
    asistencias_salir(true, "Asistencia guardada correctamente para " . count($registros) . " estudiante(s).", $idModulo, $fecha, $isAjax);
} else {
    asistencias_salir(false, "Error al guardar la asistencia. Inténtelo de nuevo.", $idModulo, $fecha, $isAjax);
}

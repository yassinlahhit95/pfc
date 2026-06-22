<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$idProfesor   = (int)$_SESSION['idProfesor'];
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$idModulo     = (int)($_POST['idModulo'] ?? 0);
$fecha        = $_POST['fecha'] ?? '';

// IDOR: verify module access
if ($idModulo && $esTutor && $idCicloTutor) {
    if (!moduloPerteneceACiclo($idModulo, $idCicloTutor)) {
        $_SESSION['errores'] = "No tienes permiso sobre este módulo.";
        header("Location: ../../../vistas/profesores/asistencias/registrar.php");
        exit;
    }
}

if (!$idModulo || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $_SESSION['errores'] = "Datos no válidos.";
    header("Location: ../../../vistas/profesores/asistencias/registrar.php");
    exit;
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
    $_SESSION['errores'] = "No se recibieron registros.";
    header("Location: ../../../vistas/profesores/asistencias/registrar.php?idModulo=$idModulo&fecha=$fecha");
    exit;
}

if (guardarAsistenciasMasivo($idModulo, $idProfesor, $fecha, $registros)) {
    registrarAccion('registrar_asistencia', 'asistencias', $idModulo, "Módulo #$idModulo · $fecha · " . count($registros) . " alumnos");
    $_SESSION['exito'] = "Asistencia guardada correctamente para " . count($registros) . " estudiante(s).";
} else {
    $_SESSION['errores'] = "Error al guardar la asistencia. Inténtelo de nuevo.";
}

header("Location: ../../../vistas/profesores/asistencias/registrar.php?idModulo=$idModulo&fecha=$fecha");
exit;

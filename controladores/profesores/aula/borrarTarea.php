<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = (int)$_SESSION['idProfesor'];
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken()) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$idTarea = (int)($_POST['idTarea'] ?? 0);
$tarea = $idTarea > 0 ? obtenerTareaPorIdAula($idTarea) : null;

if ($tarea) {
    $misModulos = listarModulosDeProfesor($idProfesor);
    if (!in_array((int)$tarea['idModulo'], array_column($misModulos, 'idModulo'))) {
        $msg = "No tienes permiso para eliminar esta tarea.";
    } elseif (eliminarTareaAula($idTarea)) {
        $ok = true; $msg = "La tarea y sus entregas han sido eliminadas.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "No se pudo eliminar la tarea.";
        $_SESSION['errores'] = $msg;
    }
} else {
    $msg = "Tarea no encontrada.";
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/profesores/aula/tareas.php?idModulo=" . (int)($tarea['idModulo'] ?? 0));
exit;

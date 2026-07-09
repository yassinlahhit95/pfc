<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = (int)$_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$idTarea = (int)($_POST['idTarea'] ?? 0);
$tarea = $idTarea > 0 ? obtenerTareaPorIdAula($idTarea) : null;

if (!$tarea) {
    $_SESSION['errores'] = "Tarea no encontrada.";
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$volver = "../../../vistas/profesores/aula/tareas.php?idModulo=" . (int)$tarea['idModulo'];

$misModulos = listarModulosDeProfesor($idProfesor);
if (!in_array((int)$tarea['idModulo'], array_column($misModulos, 'idModulo'))) {
    $_SESSION['errores'] = "No tienes permiso para gestionar esta tarea.";
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$nuevoEstado = empty($tarea['publicado']) ? 1 : 0;

if (actualizarPublicadoTareaAula($idTarea, $nuevoEstado)) {
    if ($nuevoEstado) {
        notificarEstudiantesPorModulo((int)$tarea['idModulo'], 'tarea_nueva', 'Nueva Tarea',
            "Se ha publicado una nueva tarea: {$tarea['titulo']}", $idTarea, 'TAREA');
        $_SESSION['exito'] = "Tarea publicada. Los estudiantes han sido notificados.";
    } else {
        $_SESSION['exito'] = "Tarea ocultada. Los estudiantes ya no la verán.";
    }
} else {
    $_SESSION['errores'] = "No se pudo cambiar el estado de la tarea.";
}

header("Location: $volver");
exit;

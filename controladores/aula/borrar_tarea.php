<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];
$idTarea = (int)($_GET['id'] ?? 0);

$tarea = obtenerTareaPorIdAula($idTarea);

if (!$tarea || $tarea['idProfesor'] != $idProfesor) {
    header("Location: ../../vistas/profesores/aula/tareas.php");
    exit;
}

// Delete task file if exists
if ($tarea['archivoAdjunto']) {
    $ruta = __DIR__ . "/../../public/uploads/aula/tareas/" . $tarea['archivoAdjunto'];
    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

$borrado = borrarTareaAula($idTarea);

if ($borrado) {
    $_SESSION['exito'] = 'Tarea eliminada exitosamente';
    Logger::activity('TAREA_ELIMINADA', $idProfesor, ['idTarea' => $idTarea, 'titulo' => $tarea['titulo']]);
} else {
    $_SESSION['errores'] = 'Error al eliminar la tarea. Intenta de nuevo.';
    Logger::error('Error eliminando tarea', ['profesor' => $idProfesor, 'tarea' => $idTarea]);
}

header("Location: ../../vistas/profesores/aula/tareas.php");
?>

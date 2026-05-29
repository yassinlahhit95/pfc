<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/profesores/aula/tareas.php");
    exit;
}

Security::validateCSRFToken($_POST['csrf_token'] ?? '') or die('CSRF validation failed');

$idTarea = (int)($_POST['id'] ?? 0);
$titulo = Security::sanitize($_POST['titulo'] ?? '');
$descripcion = Security::sanitize($_POST['descripcion'] ?? '');
$publicar = isset($_POST['publicar']) ? 1 : 0;

$tarea = obtenerTareaPorIdAula($idTarea);

if (!$tarea || $tarea['idProfesor'] != $idProfesor) {
    header("Location: ../../vistas/profesores/aula/tareas.php");
    exit;
}

$errores = [];

if (empty($titulo)) $errores[] = 'El título es requerido';
if (empty($descripcion)) $errores[] = 'La descripción es requerida';

if (!empty($errores)) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header("Location: ../../vistas/profesores/aula/editar_tarea.php?id=$idTarea");
    Logger::warning('Validación fallida en actualizar_tarea', ['profesor' => $idProfesor, 'tarea' => $idTarea]);
    exit;
}

$archivoAdjunto = $tarea['archivoAdjunto'];

if (isset($_FILES['archivo']) && $_FILES['archivo']['size'] > 0) {
    $archivo = $_FILES['archivo'];
    $permitidas = ['pdf', 'doc', 'docx', 'zip', 'rar', 'txt'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidas)) {
        $_SESSION['errores'] = 'Tipo de archivo no permitido';
        header("Location: ../../vistas/profesores/aula/editar_tarea.php?id=$idTarea");
        exit;
    }

    if ($archivo['size'] > 20 * 1024 * 1024) {
        $_SESSION['errores'] = 'Archivo muy grande (máx 20MB)';
        header("Location: ../../vistas/profesores/aula/editar_tarea.php?id=$idTarea");
        exit;
    }

    if ($archivoAdjunto) {
        $rutaAntigua = __DIR__ . "/../../public/uploads/aula/tareas/$archivoAdjunto";
        if (file_exists($rutaAntigua)) unlink($rutaAntigua);
    }

    $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
    $ruta = __DIR__ . "/../../public/uploads/aula/tareas/$nombreArchivo";

    if (!file_exists(dirname($ruta))) {
        mkdir(dirname($ruta), 0755, true);
    }

    if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
        $archivoAdjunto = $nombreArchivo;
    }
}

$actualizado = editarTareaAula($idTarea, $titulo, $descripcion);

if ($actualizado) {
    // Update publicado status if changed
    if ($publicar != $tarea['publicada']) {
        togglePublicadoTareaAula($idTarea);
    }

    $_SESSION['exito'] = 'Tarea actualizada exitosamente';
    Logger::activity('TAREA_ACTUALIZADA', $idProfesor, ['idTarea' => $idTarea, 'titulo' => $titulo]);
    header("Location: ../../vistas/profesores/aula/tareas.php");
} else {
    $_SESSION['errores'] = 'Error al actualizar la tarea. Intenta de nuevo.';
    Logger::error('Error actualizando tarea', ['profesor' => $idProfesor, 'tarea' => $idTarea]);
    header("Location: ../../vistas/profesores/aula/editar_tarea.php?id=$idTarea");
}
?>

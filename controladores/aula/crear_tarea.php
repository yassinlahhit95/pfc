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
    header("Location: ../../vistas/profesores/aula/crear_tarea.php");
    exit;
}

Security::validateCSRFToken($_POST['csrf_token'] ?? '') or die('CSRF validation failed');

$idModulo = (int)($_POST['idModulo'] ?? 0);
$titulo = Security::sanitize($_POST['titulo'] ?? '');
$descripcion = Security::sanitize($_POST['descripcion'] ?? '');
$publicar = isset($_POST['publicar']) ? 1 : 0;

$errores = [];

if (empty($titulo)) $errores[] = 'El título es requerido';
if (empty($descripcion)) $errores[] = 'La descripción es requerida';
if ($idModulo <= 0) $errores[] = 'Debes seleccionar un módulo';

if (!empty($errores)) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header("Location: ../../vistas/profesores/aula/crear_tarea.php");
    Logger::warning('Validación fallida en crear_tarea', ['profesor' => $idProfesor]);
    exit;
}

$archivoAdjunto = null;
if (isset($_FILES['archivo']) && $_FILES['archivo']['size'] > 0) {
    $archivo = $_FILES['archivo'];
    $permitidas = ['pdf', 'doc', 'docx', 'zip', 'rar', 'txt'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $permitidas)) {
        $_SESSION['errores'] = 'Tipo de archivo no permitido';
        header("Location: ../../vistas/profesores/aula/crear_tarea.php");
        exit;
    }

    if ($archivo['size'] > 20 * 1024 * 1024) {
        $_SESSION['errores'] = 'Archivo muy grande (máx 20MB)';
        header("Location: ../../vistas/profesores/aula/crear_tarea.php");
        exit;
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

$idTarea = insertarTareaAula($titulo, $descripcion, $idModulo, $idProfesor, $archivoAdjunto);

if ($idTarea) {
    if ($publicar) {
        togglePublicadoTareaAula($idTarea);
    }

    $_SESSION['exito'] = 'Tarea creada exitosamente';
    Logger::activity('TAREA_CREADA', $idProfesor, ['idTarea' => $idTarea, 'titulo' => $titulo]);

    if ($publicar) {
        notificarEstudiantesPorModulo($idModulo, 'NUEVA_TAREA', 'Nueva Tarea', "Se ha publicado una nueva tarea: $titulo", $idTarea, 'TAREA');
    }

    header("Location: ../../vistas/profesores/aula/tareas.php");
} else {
    $_SESSION['errores'] = 'Error al crear la tarea. Intenta de nuevo.';
    Logger::error('Error creando tarea', ['profesor' => $idProfesor, 'titulo' => $titulo]);
    header("Location: ../../vistas/profesores/aula/crear_tarea.php");
}
?>

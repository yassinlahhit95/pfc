<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/Logger.php";

$idEstudiante = $_SESSION['idEstudiante'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/estudiantes/aula/tareas.php");
    exit;
}

Security::validateCSRFToken($_POST['csrf_token'] ?? '') or die('CSRF validation failed');

$idTarea = (int)($_POST['idTarea'] ?? 0);
$respuesta = Security::sanitize($_POST['respuesta'] ?? '');

if (!isset($_FILES['archivo']) || $_FILES['archivo']['size'] == 0) {
    $_SESSION['errores'] = 'Debes subir un archivo';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

$archivo = $_FILES['archivo'];
$permitidas = ['pdf', 'doc', 'docx', 'zip', 'rar', 'txt'];
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $permitidas)) {
    $_SESSION['errores'] = 'Tipo de archivo no permitido';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

if ($archivo['size'] > 10 * 1024 * 1024) {
    $_SESSION['errores'] = 'Archivo muy grande (máx 10MB)';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

$nombreArchivo = uniqid() . '_' . basename($archivo['name']);
$ruta = __DIR__ . "/../../../public/uploads/aula/entregas/$nombreArchivo";

if (!file_exists(dirname($ruta))) {
    mkdir(dirname($ruta), 0755, true);
}

if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
    $_SESSION['errores'] = 'Error al subir el archivo';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

$idEntrega = enviarEntregaAula($idTarea, $idEstudiante, $nombreArchivo, $respuesta);

if ($idEntrega) {
    $_SESSION['exito'] = 'Entrega registrada exitosamente';
    Logger::activity('ENTREGA_ENVIADA', $idEstudiante, ['idEntrega' => $idEntrega, 'idTarea' => $idTarea]);

    // Notify teacher
    $tarea = obtenerTareaPorIdAula($idTarea);
    insertarNotificacionAula($tarea['idProfesor'], 'profesor', 'ENTREGA_NUEVA', 'Nueva Entrega', 'Un estudiante ha entregado la tarea: ' . $tarea['titulo'], $idEntrega, 'ENTREGA');

    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
} else {
    $_SESSION['errores'] = 'Error al registrar la entrega. Intenta de nuevo.';
    Logger::error('Error enviando entrega', ['estudiante' => $idEstudiante, 'tarea' => $idTarea]);
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
}
?>

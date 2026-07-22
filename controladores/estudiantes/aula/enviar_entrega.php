<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/Logger.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$idEstudiante = $_SESSION['idEstudiante'];

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/estudiantes/aula/tareas.php");
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = "Solicitud inválida (error de seguridad). Por favor, intenta de nuevo.";
    header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=" . (int)($_POST['idTarea'] ?? 0));
    exit;
}

$idTarea   = (int)($_POST['idTarea'] ?? 0);
$respuesta = Security::sanitize($_POST['respuesta'] ?? '');

// Verificar que el estudiante pertenece al ciclo de la tarea (evita IDOR)
$tarea           = obtenerTareaPorIdAula($idTarea);
$datosEstudiante = obtenerEstudiantePorId($idEstudiante);
if (!$tarea || !$datosEstudiante || $datosEstudiante['idCiclo'] != $tarea['idCiclo']) {
    $_SESSION['errores'] = "No tienes acceso a esta tarea.";
    header("Location: ../../../vistas/estudiantes/aula/tareas.php");
    exit;
}

if (!isset($_FILES['archivo']) || $_FILES['archivo']['size'] == 0) {
    $_SESSION['errores'] = 'Debes subir un archivo.';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

$archivo    = $_FILES['archivo'];
$permitidas = ['pdf', 'doc', 'docx', 'zip', 'rar', 'txt'];
$extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

if (!in_array($extension, $permitidas)) {
    $_SESSION['errores'] = 'Tipo de archivo no permitido.';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

if ($archivo['size'] > 10 * 1024 * 1024) {
    $_SESSION['errores'] = 'El archivo supera el límite de 10 MB.';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$nombreArchivo = bin2hex(random_bytes(12)) . '.' . $extension;
$tmpName       = $archivo['tmp_name'];

$mimeReal = @mime_content_type($tmpName) ?: 'application/octet-stream';
$bytes    = file_get_contents($tmpName);
$subioOk  = $bytes !== false && R2Client::putObject('aula/entregas/' . $nombreArchivo, $bytes, $mimeReal);
@unlink($tmpName);

if (!$subioOk) {
    $_SESSION['errores'] = 'Error al subir el archivo.';
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
    exit;
}

$idEntrega = enviarEntregaAula($idTarea, $idEstudiante, $nombreArchivo, $respuesta);

if ($idEntrega) {
    $_SESSION['exito'] = 'Entrega registrada exitosamente';
    Logger::activity('ENTREGA_ENVIADA', $idEstudiante, ['idEntrega' => $idEntrega, 'idTarea' => $idTarea]);

    // Notificar al profesor
    insertarNotificacionAula($tarea['idProfesor'], 'profesor', 'entrega_enviada', 'Nueva Entrega', 'Un estudiante ha entregado la tarea: ' . $tarea['titulo'], $idEntrega, 'ENTREGA');

    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
} else {
    R2Client::deleteObject('aula/entregas/' . $nombreArchivo); // el archivo se subió pero el registro en BD falló — eliminar huérfano
    $_SESSION['errores'] = 'Error al registrar la entrega. Inténtalo de nuevo.';
    Logger::error('Error enviando entrega', ['estudiante' => $idEstudiante, 'tarea' => $idTarea]);
    header("Location: ../../../vistas/estudiantes/aula/tarea_detalle.php?id=$idTarea");
}

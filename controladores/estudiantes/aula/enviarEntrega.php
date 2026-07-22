<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/Logger.php";
require_once __DIR__ . "/../../../include/R2Client.php";

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['enviarEntrega'])) { header("Location: ../../../vistas/estudiantes/aula/recursos.php"); exit; }

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud inválida. Por favor, inténtalo de nuevo.";
    header("Location: ../../../vistas/estudiantes/aula/recursos.php");
    exit;
}

$idEstudiante = $_SESSION['idEstudiante'];
$idTarea      = intval($_POST['idTarea'] ?? 0);
$respuesta    = trim($_POST['respuesta'] ?? '');

$tarea = obtenerTareaPorIdAula($idTarea);
if (!$tarea || !$tarea['publicado']) {
    $_SESSION['errores'] = "La tarea no está disponible.";
    header("Location: ../../../vistas/estudiantes/aula/recursos.php"); exit;
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
if (!$estudiante || $estudiante['idCiclo'] != $tarea['idCiclo']) {
    $_SESSION['errores'] = "No tienes acceso a esta tarea.";
    header("Location: ../../../vistas/estudiantes/aula/recursos.php"); exit;
}

if (empty($respuesta) && empty($_FILES['archivoEntrega']['name'])) {
    $_SESSION['errores'] = "Debes escribir una respuesta o adjuntar un archivo.";
    header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$archivoEntrega = null;
if (!empty($_FILES['archivoEntrega']['name'])) {
    $archivo = $_FILES['archivoEntrega'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf','docx','txt'])) {
        $_SESSION['errores'] = "Solo se permiten archivos PDF, DOCX o TXT.";
        header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
    }
    if ($archivo['size'] > 20 * 1024 * 1024) {
        $_SESSION['errores'] = "El archivo supera el límite de 20 MB.";
        header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
    }
    $nombreArchivo = bin2hex(random_bytes(12)) . '.' . $ext;
    $tmpName  = $archivo['tmp_name'];
    $mimeReal = @mime_content_type($tmpName) ?: 'application/octet-stream';
    $bytes    = file_get_contents($tmpName);
    $subioOk  = $bytes !== false && R2Client::putObject('aula/entregas/' . $nombreArchivo, $bytes, $mimeReal);
    @unlink($tmpName);
    if ($subioOk) {
        $archivoEntrega = $nombreArchivo;
    } else {
        $_SESSION['errores'] = "Error al guardar el archivo en el servidor.";
        header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea"); exit;
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (enviarEntregaAula($idTarea, $idEstudiante, $archivoEntrega, $respuesta)) {
    $_SESSION['exito'] = "La entrega ha sido enviada correctamente.";
    Logger::activity('ENTREGA_ENVIADA', $idEstudiante, ['idTarea' => $idTarea]);
    insertarNotificacionAula(
        $tarea['idProfesor'], 'profesor', 'entrega_enviada',
        'Nueva entrega: ' . $tarea['titulo'],
        'Un estudiante ha enviado su entrega.',
        $idTarea, 'tarea'
    );
    $fh = __DIR__ . "/../../firebase/firebase_helper.php";
    if (file_exists($fh)) {
        require_once $fh;
        $token = obtenerTokenUsuario($tarea['idProfesor'], 'profesor');
        if ($token) {
            enviarNotificacionFirebase($token, 'Nueva entrega: ' . $tarea['titulo'], 'Un estudiante ha enviado su entrega.');
        }
    }
} else {
    $_SESSION['errores'] = "No se pudo guardar la entrega.";
}

header("Location: ../../../vistas/estudiantes/aula/tarea.php?id=$idTarea");
exit;

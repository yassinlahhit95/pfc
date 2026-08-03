<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/Logger.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function entrega_salir($ok, $msg, $volver, $isAjax) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'msg' => $msg]);
        exit;
    }
    if ($ok) { $_SESSION['exito'] = $msg; } else { $_SESSION['errores'] = $msg; }
    header("Location: $volver");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['enviarEntrega'])) { header("Location: ../../../vistas/estudiantes/aula/recursos.php"); exit; }

if (!Security::validateCSRFToken(null, false)) {
    entrega_salir(false, "Solicitud inválida. Por favor, inténtalo de nuevo.", "../../../vistas/estudiantes/aula/recursos.php", $isAjax);
}

$idEstudiante = $_SESSION['idEstudiante'];
$idTarea      = intval($_POST['idTarea'] ?? 0);
$respuesta    = trim($_POST['respuesta'] ?? '');
$volverTarea  = "../../../vistas/estudiantes/aula/tarea.php?id=$idTarea";

$tarea = obtenerTareaPorIdAula($idTarea);
if (!$tarea || !$tarea['publicado']) {
    entrega_salir(false, "La tarea no está disponible.", "../../../vistas/estudiantes/aula/recursos.php", $isAjax);
}

$estudiante = obtenerEstudiantePorId($idEstudiante);
if (!$estudiante || $estudiante['idCiclo'] != $tarea['idCiclo']) {
    entrega_salir(false, "No tienes acceso a esta tarea.", "../../../vistas/estudiantes/aula/recursos.php", $isAjax);
}

if (empty($respuesta) && empty($_FILES['archivoEntrega']['name'])) {
    entrega_salir(false, "Debes escribir una respuesta o adjuntar un archivo.", $volverTarea, $isAjax);
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$archivoEntrega = null;
if (!empty($_FILES['archivoEntrega']['name'])) {
    $archivo = $_FILES['archivoEntrega'];
    $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $mimeAllowed = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'application/octet-stream',
    ];
    $tmpName  = $archivo['tmp_name'];
    $mimeReal = @mime_content_type($tmpName);
    if (!in_array($ext, ['pdf','docx','txt']) || ($mimeReal && !in_array($mimeReal, $mimeAllowed))) {
        entrega_salir(false, "Solo se permiten archivos PDF, DOCX o TXT.", $volverTarea, $isAjax);
    }
    if ($archivo['size'] > 20 * 1024 * 1024) {
        entrega_salir(false, "El archivo supera el límite de 20 MB.", $volverTarea, $isAjax);
    }
    $nombreArchivo = bin2hex(random_bytes(12)) . '.' . $ext;
    $mimeReal = $mimeReal ?: 'application/octet-stream';
    $bytes    = file_get_contents($tmpName);
    $subioOk  = $bytes !== false && R2Client::putObject('aula/entregas/' . $nombreArchivo, $bytes, $mimeReal);
    @unlink($tmpName);
    if ($subioOk) {
        $archivoEntrega = $nombreArchivo;
    } else {
        entrega_salir(false, "Error al guardar el archivo en el servidor.", $volverTarea, $isAjax);
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (enviarEntregaAula($idTarea, $idEstudiante, $archivoEntrega, $respuesta)) {
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
            enviarNotificacionFirebase($token, 'Nueva entrega: ' . $tarea['titulo'], 'Un estudiante ha enviado su entrega.', 'entrega_enviada', ['idTarea' => $idTarea]);
        }
    }
    entrega_salir(true, "La entrega ha sido enviada correctamente.", $volverTarea, $isAjax);
} else {
    entrega_salir(false, "No se pudo guardar la entrega.", $volverTarea, $isAjax);
}

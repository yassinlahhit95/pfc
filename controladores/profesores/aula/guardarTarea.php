<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/ImageOptimizer.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$idProfesor = (int)$_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idTarea     = (int)($_POST['idTarea'] ?? 0);      // 0 = crear, >0 = editar
$idModulo    = (int)($_POST['idModulo'] ?? 0);
$titulo      = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$publicado   = !empty($_POST['publicado']) ? 1 : 0;

$volver = "../../../vistas/profesores/aula/tareas.php?idModulo=$idModulo";

$errores = [];
if (!$idModulo)             $errores[] = "El módulo es obligatorio.";
if ($titulo === '')         $errores[] = "El título es obligatorio.";
if (mb_strlen($titulo) > 150) $errores[] = "El título no puede superar los 150 caracteres.";

// El profesor puede gestionar tareas de módulos que imparte o de ciclos que tutoriza
$moduloInfo = obtenerModuloPorId($idModulo);
$idCicloMod = $moduloInfo['idCiclo'] ?? 0;
$misModulos = listarModulosDeProfesor($idProfesor);
$esTutorCiclo = (!empty($_SESSION['esTutor']) && !empty($_SESSION['idCicloTutor']) && $_SESSION['idCicloTutor'] == $idCicloMod);
if (!in_array($idModulo, array_column($misModulos, 'idModulo')) && !$esTutorCiclo) {
    $_SESSION['errores'] = "No tienes permiso para gestionar tareas de este módulo.";
    header("Location: ../../../vistas/profesores/aula/tareas.php");
    exit;
}

$tareaExistente = null;
$estabaPublicada = false;
if ($idTarea > 0) {
    $tareaExistente = obtenerTareaPorIdAula($idTarea);
    if (!$tareaExistente || (int)$tareaExistente['idModulo'] !== $idModulo) {
        $_SESSION['errores'] = "Tarea no encontrada.";
        header("Location: $volver");
        exit;
    }
    $estabaPublicada = !empty($tareaExistente['publicado']);
}

// ── Adjunto opcional ──
$nombreAdjunto = null;
if (!empty($_FILES['archivoAdjunto']['name'])) {
    $archivo = $_FILES['archivoAdjunto'];
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Error al subir el archivo adjunto.";
    } else {
        $ext = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $mimeAllowedAdjunto = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'application/zip',
            'image/png',
            'image/jpeg',
            'application/octet-stream',
        ];
        $mimeAdjunto = @mime_content_type($archivo['tmp_name']);
        if (!in_array($ext, ['pdf', 'docx', 'txt', 'zip', 'png', 'jpg', 'jpeg'])
            || ($mimeAdjunto && !in_array($mimeAdjunto, $mimeAllowedAdjunto))) {
            $errores[] = "Adjunto: solo se permiten PDF, DOCX, TXT, ZIP o imágenes.";
        } elseif ($archivo['size'] > 20 * 1024 * 1024) {
            $errores[] = "El adjunto supera el límite de 20 MB.";
        }
    }
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_tarea'] = $_POST;
    header("Location: $volver" . ($idTarea ? "&editar=$idTarea" : ""));
    exit;
}

if (!empty($_FILES['archivoAdjunto']['name']) && $_FILES['archivoAdjunto']['error'] === UPLOAD_ERR_OK) {
    $dir = __DIR__ . "/../../../public/uploads/aula/tareas/"; // solo para localizar/borrar adjuntos heredados
    $ext = strtolower(pathinfo($_FILES['archivoAdjunto']['name'], PATHINFO_EXTENSION));
    $nombreAdjunto = bin2hex(random_bytes(12)) . '.' . $ext;
    $tmpName = $_FILES['archivoAdjunto']['tmp_name'];

    $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
    if (isset($imgMimes[$ext])) ImageOptimizer::optimize($tmpName, $imgMimes[$ext]); // optimizar el temporal ANTES de subir a R2

    $mimeReal = @mime_content_type($tmpName) ?: 'application/octet-stream';
    $bytes    = file_get_contents($tmpName);
    $subioOk  = $bytes !== false && R2Client::putObject('aula/tareas/' . $nombreAdjunto, $bytes, $mimeReal);
    @unlink($tmpName);

    if (!$subioOk) {
        $nombreAdjunto = null;
    } elseif ($tareaExistente && !empty($tareaExistente['archivoAdjunto'])) {
        // Sustituye el adjunto anterior (en cualquiera de los dos almacenamientos)
        $rutaVieja = $dir . $tareaExistente['archivoAdjunto'];
        if (is_file($rutaVieja)) unlink($rutaVieja);
        R2Client::deleteObject('aula/tareas/' . $tareaExistente['archivoAdjunto']);
    }
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if ($idTarea > 0) {
    $ok = actualizarTareaAula($idTarea, $titulo, $descripcion, $publicado, $nombreAdjunto);
    $msgOk = "Tarea actualizada correctamente.";
} else {
    $idTarea = insertarTareaAula($idModulo, $idProfesor, $titulo, $descripcion, $nombreAdjunto, $publicado);
    $ok = $idTarea > 0;
    $msgOk = "Tarea creada correctamente.";
}

if ($ok) {
    // Avisar a los estudiantes cuando la tarea pasa a estar publicada
    if ($publicado && !$estabaPublicada) {
        notificarEstudiantesPorModulo($idModulo, 'tarea_nueva', 'Nueva Tarea',
            "Se ha publicado una nueva tarea: $titulo", $idTarea, 'TAREA');
    }
    $_SESSION['exito'] = $msgOk;
} else {
    $_SESSION['errores'] = "No se pudo guardar la tarea.";
}

header("Location: $volver");
exit;

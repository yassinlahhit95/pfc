<?php
require_once __DIR__ . "/../../../include/TutorGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$_dashboard   = "../../../vistas/tutores/inicio/dashboard.php";
$_back        = "../../../vistas/tutores/asistencias/lista.php?id=" . $idEstudiante;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_dashboard"); exit;
}

// Comprueba que el estudiante es realmente un hijo de este tutor antes de nada.
$hijos  = listarEstudiantesPorTutor((int)$_SESSION['idTutor']);
$esHijo = false;
foreach ($hijos as $hijo) {
    if ((int)$hijo['idEstudiante'] === $idEstudiante) {
        $esHijo = true;
        break;
    }
}
if (!$esHijo) {
    $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
    header("Location: $_dashboard"); exit;
}

$idAsistencia = (int)($_POST['idAsistencia'] ?? 0);
$motivo       = trim($_POST['motivo'] ?? '');

if ($idAsistencia <= 0 || $motivo === '') {
    $_SESSION['errores'] = "Indica el motivo de la justificación.";
    header("Location: $_back"); exit;
}

$con  = obtenerConexion();
$stmt = mysqli_prepare($con, "SELECT idEstudiante, estado FROM asistencias WHERE idAsistencia = ?");
mysqli_stmt_bind_param($stmt, "i", $idAsistencia);
mysqli_stmt_execute($stmt);
$asistencia = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$asistencia || (int)$asistencia['idEstudiante'] !== $idEstudiante) {
    $_SESSION['errores'] = "No tienes permiso sobre este registro de asistencia.";
    header("Location: $_back"); exit;
}
if (!in_array($asistencia['estado'], ['ausente', 'retraso'], true)) {
    $_SESSION['errores'] = "Este registro no admite justificación.";
    header("Location: $_back"); exit;
}

$existente = obtenerJustificacionPorAsistencia($idAsistencia);
if ($existente && $existente['estado'] !== 'rechazada') {
    $_SESSION['errores'] = "Ya existe una justificación para esta falta.";
    header("Location: $_back"); exit;
}

// ── Archivo adjunto (opcional) ──────────────────────────────────────────
$archivo = null;
if (!empty($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['archivo'];
    if ($file['size'] > 8 * 1024 * 1024) {
        $_SESSION['errores'] = "El archivo es demasiado grande (máximo 8 MB).";
        header("Location: $_back"); exit;
    }
    $mime = mime_content_type($file['tmp_name']);
    $mimeExtMap = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($mimeExtMap[$mime])) {
        $_SESSION['errores'] = "Formato no permitido. Sube un PDF o una imagen (JPG/PNG).";
        header("Location: $_back"); exit;
    }
    require_once __DIR__ . '/../../../include/ImageOptimizer.php';
    require_once __DIR__ . '/../../../include/R2Client.php';
    if (in_array($mime, ['image/jpeg', 'image/png'], true)) {
        ImageOptimizer::optimize($file['tmp_name'], $mime); // optimizar el temporal ANTES de subir a R2
    }
    $archivo = 'just_' . $idEstudiante . '_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
    $bytes   = file_get_contents($file['tmp_name']);
    $subioOk = $bytes !== false && R2Client::putObject('justificantes/' . $archivo, $bytes, $mime);
    @unlink($file['tmp_name']);
    if (!$subioOk) {
        $_SESSION['errores'] = "Error al subir el archivo.";
        header("Location: $_back"); exit;
    }
}

if (crearJustificacionFalta($idAsistencia, $idEstudiante, $motivo, $archivo, $asistencia['estado'], 'tutor', (int)$_SESSION['idTutor'])) {
    $_SESSION['exito'] = "Justificación enviada. El profesor la revisará en breve.";
} else {
    $_SESSION['errores'] = "No se pudo enviar la justificación. Inténtalo de nuevo.";
}
header("Location: $_back"); exit;

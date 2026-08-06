<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$_back = "../../../vistas/secretaria/asistencias/justificaciones.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

$idSecretaria = (int)$_SESSION['idSecretaria'];
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$idAsistencia = (int)($_POST['idAsistencia'] ?? 0);
$motivo       = trim($_POST['motivo'] ?? '');

if (!$idEstudiante || !$idAsistencia || !$motivo) {
    $_SESSION['errores'] = "Todos los campos son obligatorios.";
    header("Location: $_back"); exit;
}

// Obtiene el estado original del registro de asistencia
$con = obtenerConexion();
$stmt = mysqli_prepare($con, "SELECT estado FROM asistencias WHERE idAsistencia = ?");
mysqli_stmt_bind_param($stmt, "i", $idAsistencia);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$asistencia = mysqli_fetch_assoc($res);
if (!$asistencia) {
    $_SESSION['errores'] = "La asistencia seleccionada no existe.";
    header("Location: $_back"); exit;
}
$estadoOriginal = $asistencia['estado'];

// Gestiona la subida del archivo
$archivoPath = null;
if (!empty($_FILES['archivo']['name'])) {
    // Valida el tipo MIME antes de subir
    $mime = mime_content_type($_FILES['archivo']['tmp_name']);
    $allowed = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!in_array($mime, $allowed)) {
        $_SESSION['errores'] = 'Solo se permiten PDF e imágenes (JPEG, PNG).';
        header("Location: $_back"); exit;
    }

    $targetDir = __DIR__ . "/../../../public/uploads/justificantes/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $filename = bin2hex(random_bytes(6)) . '.' . pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $targetDir . $filename)) {
        $archivoPath = 'public/uploads/justificantes/' . $filename;
    }
}

$idJustificacion = crearJustificacionFalta($idAsistencia, $idEstudiante, $motivo, $archivoPath, $estadoOriginal, 'secretaria', $idSecretaria);
if ($idJustificacion) {
    // Auto-aprobar, ya que lo registra secretaría
    resolverJustificacionFalta($idJustificacion, $idAsistencia, true, $idSecretaria, '', $estadoOriginal, 'secretaria');
    $_SESSION['exito'] = "Justificación registrada y aprobada con éxito.";
} else {
    $_SESSION['errores'] = "No se pudo registrar la justificación.";
}

header("Location: $_back"); exit;

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

// Get the original attendance record state
$con = obtenerConexion();
$res = mysqli_query($con, "SELECT estado FROM asistencias WHERE idAsistencia = $idAsistencia");
$asistencia = mysqli_fetch_assoc($res);
if (!$asistencia) {
    $_SESSION['errores'] = "La asistencia seleccionada no existe.";
    header("Location: $_back"); exit;
}
$estadoOriginal = $asistencia['estado'];

// Handle file upload
$archivoPath = null;
if (!empty($_FILES['archivo']['name'])) {
    $targetDir = __DIR__ . "/../../../public/uploads/justificantes/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $ext = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('justif_') . '.' . $ext;
    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $targetDir . $filename)) {
        $archivoPath = 'public/uploads/justificantes/' . $filename;
    }
}

$idJustificacion = crearJustificacionFalta($idAsistencia, $idEstudiante, $motivo, $archivoPath, $estadoOriginal, 'secretaria', $idSecretaria);
if ($idJustificacion) {
    // Auto-approve since this is registered by secretariat
    resolverJustificacionFalta($idJustificacion, $idAsistencia, true, $idSecretaria, '', $estadoOriginal, 'secretaria');
    $_SESSION['exito'] = "Justificación registrada y aprobada con éxito.";
} else {
    $_SESSION['errores'] = "No se pudo registrar la justificación.";
}

header("Location: $_back"); exit;

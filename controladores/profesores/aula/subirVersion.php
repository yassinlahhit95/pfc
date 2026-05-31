<?php
// Sube una nueva versión de un recurso existente conservando el historial (#8)
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idProfesor = $_SESSION['idProfesor'];
$idArchivo  = intval($_POST['idArchivo'] ?? 0);
$idModulo   = intval($_POST['idModulo'] ?? 0);

$archivo = $idArchivo > 0 ? obtenerArchivoPorId($idArchivo) : null;
if (!$archivo || $archivo['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No puedes actualizar este archivo.";
    header("Location: ../../../vistas/profesores/aula/recursos.php?id=$idModulo"); exit;
}

$permitidos = [
    'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt',
    'xls', 'xlsx', 'ods', 'csv',
    'ppt', 'pptx', 'odp',
    'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
    'zip', 'rar'
];

if (empty($_FILES['archivo']['name']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['errores'] = "No se recibió ningún archivo.";
    header("Location: ../../../vistas/profesores/aula/recursos.php?id={$archivo['idModulo']}"); exit;
}

$nombreOrig = $_FILES['archivo']['name'];
$ext        = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
$tamanio    = $_FILES['archivo']['size'];

if (!in_array($ext, $permitidos)) {
    $_SESSION['errores'] = "Tipo de archivo no permitido ($ext).";
} elseif ($tamanio > 20 * 1024 * 1024) {
    $_SESSION['errores'] = "El archivo supera el límite de 20 MB.";
} else {
    $dir = __DIR__ . "/../../../public/uploads/aula/archivos/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $nombreArchivo = 'AULA_' . $idProfesor . '_' . date('dmY_His') . '_' . mt_rand(100,999) . '.' . $ext;
    if (move_uploaded_file($_FILES['archivo']['tmp_name'], $dir . $nombreArchivo)) {
        $nuevaVersion = actualizarArchivoConVersionAula($idArchivo, $nombreArchivo, $nombreOrig, $ext, $tamanio, $idProfesor);
        if ($nuevaVersion) {
            $_SESSION['exito'] = "Nueva versión (v$nuevaVersion) guardada.";
        } else {
            $_SESSION['errores'] = "No se pudo registrar la nueva versión.";
        }
    } else {
        $_SESSION['errores'] = "Error al guardar el archivo.";
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id={$archivo['idModulo']}";
if (!empty($archivo['idCarpeta'])) $destino .= "&carpeta=" . $archivo['idCarpeta'];
header("Location: $destino");
exit;

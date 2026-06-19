<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_csv'])) {
    header("Location: ../../../vistas/admin/profesores/verProfesores.php");
    exit;
}

$file = $_FILES['archivo_csv'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['errores'] = "Error al subir el archivo CSV.";
    header("Location: ../../../vistas/admin/profesores/verProfesores.php");
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if ($ext !== 'csv') {
    $_SESSION['errores'] = "Solo se permiten archivos CSV.";
    header("Location: ../../../vistas/admin/profesores/verProfesores.php");
    exit;
}

$handle = fopen($file['tmp_name'], 'r');
// Detect and skip BOM
$bom = fread($handle, 3);
if ($bom !== "\xEF\xBB\xBF") rewind($handle);

$header = fgetcsv($handle);
if (!$header) {
    $_SESSION['errores'] = "El archivo CSV está vacío o tiene un formato incorrecto.";
    header("Location: ../../../vistas/admin/profesores/verProfesores.php");
    exit;
}

$header = array_map(fn($h) => strtolower(trim($h)), $header);

$insertados = 0;
$omitidos   = 0;
$errLineas  = [];
$lineaNum   = 1;

while (($row = fgetcsv($handle)) !== false) {
    $lineaNum++;
    if (count($row) < 2) continue;
    $data = array_combine($header, array_pad($row, count($header), ''));

    $nombre   = trim($data['nombreprofesor'] ?? $data['nombre'] ?? '');
    $email    = trim($data['emailprofesor'] ?? $data['email'] ?? '');
    $dni      = trim($data['dniprofesor'] ?? $data['dni'] ?? '');
    $tel      = trim($data['telefonoprofesor'] ?? $data['telefono'] ?? '');
    $dir      = trim($data['direccionprofesor'] ?? $data['direccion'] ?? '');
    $ciudad   = trim($data['ciudadprofesor'] ?? $data['ciudad'] ?? '');
    $cp       = trim($data['codigopostalprofesor'] ?? $data['cp'] ?? '');
    $fNac     = trim($data['fechanacimientoprofesor'] ?? $data['fechanacimiento'] ?? '');
    $fAlta    = trim($data['fechaaltaprofesor'] ?? $data['fechaalta'] ?? date('Y-m-d'));
    $obs      = trim($data['observacionesprofesor'] ?? $data['observaciones'] ?? '');

    if (empty($nombre) || empty($email)) {
        $errLineas[] = "Línea $lineaNum: nombre o email vacío.";
        $omitidos++;
        continue;
    }

    if (checkProfesorExistente($dni ?: 'NOEXISTE', $email)) {
        $omitidos++;
        continue;
    }

    $fNac  = $fNac  ?: null;
    $fAlta = $fAlta ?: date('Y-m-d');

    $id = insertarProfesor($nombre, $email, $tel, $dni, $dir, $fNac, $fAlta, $ciudad, $cp, $obs);
    if ($id) {
        $insertados++;
    } else {
        $errLineas[] = "Línea $lineaNum: error al insertar '$nombre'.";
        $omitidos++;
    }
}

fclose($handle);

$msg = "Importación completada: $insertados profesor(es) añadidos, $omitidos omitidos.";
if (!empty($errLineas)) {
    $msg .= " Problemas: " . implode(' | ', array_slice($errLineas, 0, 5));
}
$_SESSION['exito'] = $msg;
header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;

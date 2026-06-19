<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_csv'])) {
    header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
    exit;
}

$file = $_FILES['archivo_csv'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['errores'] = "Error al subir el archivo CSV.";
    header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
    exit;
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['csv'])) {
    $_SESSION['errores'] = "Solo se permiten archivos CSV.";
    header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
    exit;
}

// Build ciclo name→id map for lookups
$ciclos = listarTodosLosCiclos();
$mapaCiclos = [];
foreach ($ciclos as $c) {
    $mapaCiclos[strtolower(trim($c['nombreCiclo']))] = $c['idCiclo'];
}

$handle = fopen($file['tmp_name'], 'r');
// Detect and skip BOM
$bom = fread($handle, 3);
if ($bom !== "\xEF\xBB\xBF") rewind($handle);

$header = fgetcsv($handle);
if (!$header) {
    $_SESSION['errores'] = "El archivo CSV está vacío o tiene un formato incorrecto.";
    header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
    exit;
}

// Normalize header keys
$header = array_map(fn($h) => strtolower(trim($h)), $header);

$insertados = 0;
$omitidos   = 0;
$errLineas  = [];
$lineaNum   = 1;

while (($row = fgetcsv($handle)) !== false) {
    $lineaNum++;
    if (count($row) < 2) continue;
    $data = array_combine($header, array_pad($row, count($header), ''));

    $nombre   = trim($data['nombreestudiante'] ?? $data['nombre'] ?? '');
    $email    = trim($data['emailestudiante'] ?? $data['email'] ?? '');
    $dni      = trim($data['dniestudiante'] ?? $data['dni'] ?? '');
    $tel      = trim($data['telefonoestudiante'] ?? $data['telefono'] ?? '');
    $dir      = trim($data['direccionestudiante'] ?? $data['direccion'] ?? '');
    $ciudad   = trim($data['ciudadestudiante'] ?? $data['ciudad'] ?? '');
    $cp       = trim($data['codigopостalestudiante'] ?? $data['codigopostalestudiante'] ?? $data['cp'] ?? '');
    $fNac     = trim($data['fechanacimientoestudiante'] ?? $data['fechanacimiento'] ?? '');
    $fAlta    = trim($data['fechaaltaestudiante'] ?? $data['fechaalta'] ?? date('Y-m-d'));
    $curso    = trim($data['curso'] ?? 'Grado Medio');
    $cicloNom = trim($data['nombreciclo'] ?? $data['ciclo'] ?? '');
    $obs      = trim($data['observacionesestudiante'] ?? $data['observaciones'] ?? '');

    if (empty($nombre) || empty($email)) {
        $errLineas[] = "Línea $lineaNum: nombre o email vacío.";
        $omitidos++;
        continue;
    }

    $idCiclo = $mapaCiclos[strtolower($cicloNom)] ?? null;
    if (!$idCiclo) {
        $errLineas[] = "Línea $lineaNum: ciclo '$cicloNom' no encontrado.";
        $omitidos++;
        continue;
    }

    // Check if student already exists by email
    $con = obtenerConexion();
    $stmtCheck = mysqli_prepare($con, "SELECT idEstudiante FROM estudiantes WHERE emailEstudiante = ?");
    mysqli_stmt_bind_param($stmtCheck, "s", $email);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);
    if (mysqli_num_rows($resCheck) > 0) {
        $omitidos++;
        continue;
    }

    $fNac  = $fNac  ?: null;
    $fAlta = $fAlta ?: date('Y-m-d');

    $id = insertarEstudiante($nombre, $email, $tel, $fNac, $dni, $fAlta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso);
    if ($id) {
        $insertados++;
    } else {
        $errLineas[] = "Línea $lineaNum: error al insertar '$nombre'.";
        $omitidos++;
    }
}

fclose($handle);

$msg = "Importación completada: $insertados estudiante(s) añadidos, $omitidos omitidos.";
if (!empty($errLineas)) {
    $msg .= " Problemas: " . implode(' | ', array_slice($errLineas, 0, 5));
}
$_SESSION['exito'] = $msg;
header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;

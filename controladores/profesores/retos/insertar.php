<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once "../../../modelos/retos.php";

if (!isset($_POST['insertarReto'])) {
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nombreReto           = trim($_POST['nombreReto']);
$fechaInicio          = trim($_POST['fechaInicio']);
$fechaFin             = trim($_POST['fechaFin']);
$horasReto            = trim($_POST['horasReto']);
$modulosSeleccionados = $_POST['modulos'] ?? [];

$errores = '';

if (empty($nombreReto)) $errores = "Falta el nombre del reto";
if (empty($fechaInicio)) $errores = "La fecha de inicio es obligatoria.";
if (empty($fechaFin)) $errores = "Falta la fecha de fin";
if (empty($horasReto)) {
    $errores = "Horas requeridas";
} elseif (!is_numeric($horasReto)) {
    $errores = "Pon un número de horas";
}

// Validar que las horas no superen el máximo según días laborables
if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicio <= $fechaFin) {
    $fechaInicioObj = new DateTime($fechaInicio);
    $fechaFinObj    = new DateTime($fechaFin);
    $diasLaborables = 0;

    $tempIter = clone $fechaInicioObj;
    while ($tempIter <= $fechaFinObj) {
        if ($tempIter->format('N') < 6) {
            $diasLaborables++;
        }
        $tempIter->modify('+1 day');
    }

    $maxHorasPermitidas = $diasLaborables * 6;
    if ($horasReto > $maxHorasPermitidas) {
        $errores = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
    }
}

if (empty($modulosSeleccionados)) {
    $errores = "Selecciona al menos un módulo";
} else if (is_numeric($horasReto)) {
    foreach ($modulosSeleccionados as $idModulo) {
        $detalle = obtenerDetalleHorasModulo($idModulo);
        if ($horasReto > $detalle['disponibles']) {
            $errores = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
            break;
        }
    }
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_reto'] = $_POST;
    header("Location: ../../../vistas/profesores/retos/agregar.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$resultado = insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulosSeleccionados);
if ($resultado) {
    $idNuevoReto = mysqli_insert_id(obtenerConexion());

    if (!empty($_FILES['archivosReto']['name'][0])) {
        $uploadDir = __DIR__ . "/../../../public/uploads/retos/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $permitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif', 'zip'];
        foreach ($_FILES['archivosReto']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['archivosReto']['error'][$key] !== UPLOAD_ERR_OK) continue;
            $fileName = $_FILES['archivosReto']['name'][$key];
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($fileExt, $permitidos)) continue;
            $newFileName = bin2hex(random_bytes(8)) . '.' . $fileExt;
            if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                $tipo = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif']) ? 'imagen' : 'pdf';
                registrarArchivoReto($idNuevoReto, $fileName, "public/uploads/retos/" . $newFileName, $tipo);
            }
        }
    }

    $_SESSION['exito'] = "Reto insertado correctamente.";
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
} else {
    $_SESSION['errores'] = "Error al insertar el reto.";
    $_SESSION['datos_reto'] = $_POST;
    header("Location: ../../../vistas/profesores/retos/agregar.php");
    exit;
}

<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once "../../../modelos/retos.php";
require_once "../../../modelos/modulos.php";

if (!isset($_POST['insertarReto'])) {
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/retos/agregar.php");
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

$errores = [];

if (empty($nombreReto))  $errores['nombreReto']  = "Falta el nombre del reto.";
if (empty($fechaInicio)) $errores['fechaInicio'] = "La fecha de inicio es obligatoria.";
if (empty($fechaFin))    $errores['fechaFin']    = "Falta la fecha de fin.";
if (empty($horasReto)) {
    $errores['horasReto'] = "Las horas son requeridas.";
} elseif (!is_numeric($horasReto)) {
    $errores['horasReto'] = "Pon un número de horas válido.";
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
        $errores['horasReto'] = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
    }
}

// IDOR: verify all submitted modules belong to this professor
if (!empty($modulosSeleccionados)) {
    $misModulos     = listarModulosDeProfesor($_SESSION['idProfesor']);
    $misModulosIds  = array_column($misModulos, 'idModulo');
    foreach ($modulosSeleccionados as $idMod) {
        if (!in_array((int)$idMod, array_map('intval', $misModulosIds), true)) {
            $errores['modulos'] = "Módulo no válido seleccionado.";
            break;
        }
    }
}

if (empty($modulosSeleccionados)) {
    $errores['modulos'] = "Selecciona al menos un módulo.";
} elseif (is_numeric($horasReto)) {
    foreach ($modulosSeleccionados as $idModulo) {
        $detalle = obtenerDetalleHorasModulo($idModulo);
        if ($horasReto > $detalle['disponibles']) {
            $errores['modulos'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
            break;
        }
    }
}

if (!empty($errores)) {
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

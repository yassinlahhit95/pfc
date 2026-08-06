<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../include/upload_helpers.php";

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

if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicio <= $fechaFin) {
    $maxHorasPermitidas = calcularMaxHorasLaborables($fechaInicio, $fechaFin);
    if ($horasReto > $maxHorasPermitidas) {
        $errores['horasReto'] = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
    }
}

// IDOR: comprobar que todos los módulos enviados pertenecen a este profesor
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

    procesarArchivosReto($idNuevoReto);

    $_SESSION['exito'] = "Reto insertado correctamente.";
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
} else {
    $_SESSION['errores'] = "Error al insertar el reto.";
    $_SESSION['datos_reto'] = $_POST;
    header("Location: ../../../vistas/profesores/retos/agregar.php");
    exit;
}

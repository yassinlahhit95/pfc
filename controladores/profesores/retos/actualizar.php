<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../include/upload_helpers.php";

if (!isset($_POST['actualizarReto'])) {
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idReto               = (int)($_POST['idReto'] ?? 0);
$nombreReto           = trim($_POST['nombreReto']);
$fechaInicio          = trim($_POST['fechaInicio']);
$fechaFin             = trim($_POST['fechaFin']);
$horasReto            = trim($_POST['horasReto']);
$modulosSeleccionados = $_POST['modulos'] ?? [];

$errores = [];

if (empty($idReto)) {
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
}

if (empty($nombreReto))  $errores['nombreReto']  = "El nombre es obligatorio.";
if (empty($fechaInicio)) $errores['fechaInicio'] = "La fecha de inicio es requerida.";
if (empty($fechaFin))    $errores['fechaFin']    = "La fecha de fin es obligatoria.";
if (empty($horasReto)) {
    $errores['horasReto'] = "Las horas son obligatorias.";
} elseif (!is_numeric($horasReto)) {
    $errores['horasReto'] = "Las horas deben ser un número.";
}

if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicio <= $fechaFin) {
    $maxHorasPermitidas = calcularMaxHorasLaborables($fechaInicio, $fechaFin);
    if ($horasReto > $maxHorasPermitidas) {
        $errores['horasReto'] = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
    }
}

if (empty($modulosSeleccionados)) {
    $errores['modulos'] = "Selecciona al menos un módulo.";
} elseif (is_numeric($horasReto)) {
    foreach ($modulosSeleccionados as $idModulo) {
        $detalle = obtenerDetalleHorasModulo($idModulo, $idReto);
        if ($horasReto > $detalle['disponibles']) {
            if ($detalle['disponibles'] < 0) {
                $errores['modulos'] = "El módulo '{$detalle['nombreModulo']}' ya supera su capacidad ({$detalle['maximo']}h). Otros retos suman {$detalle['ocupadas']}h. Libere horas antes de continuar.";
            } else {
                $errores['modulos'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
            }
            break;
        }
    }
}

if (!empty($errores)) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_reto'] = $_POST;
    header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$_esTutor      = !empty($_SESSION['esTutor']);
$_idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$_autorizado   = $_esTutor && $_idCicloTutor
    ? retoPerteneceACiclo($idReto, $_idCicloTutor)
    : retoPerteneceAProfesor($idReto, $_SESSION['idProfesor']);
if (!$_autorizado) {
    $_SESSION['errores'] = "No tienes permiso sobre este reto.";
    header("Location: ../../../vistas/profesores/retos/lista.php");
    exit;
}

$resultado = actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulosSeleccionados);
if ($resultado) {
    procesarArchivosReto($idReto);

    $_SESSION['exito'] = "Reto actualizado correctamente.";
    // editar.php, no lista.php — el formulario se envía por AJAX y su
    // success handler hace window.location.reload() (se queda en la misma
    // página para que el profesor vea el archivo recién subido en la
    // lista), así que redirigir a lista.php dejaba el mensaje de éxito
    // huérfano: se consumía en la respuesta seguida por el XHR, pero esa
    // respuesta nunca llegaba a mostrarse de verdad.
    header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
    exit;
} else {
    $_SESSION['errores'] = "Error al actualizar el reto.";
    header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
    exit;
}

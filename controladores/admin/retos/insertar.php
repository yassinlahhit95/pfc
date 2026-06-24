<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/upload_helpers.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarReto'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/retos/agregarRetos.php");
        exit;
    }
    $nombre      = trim($_POST['nombreReto']);
    $horas       = trim($_POST['horasReto']);
    $fechaInicio = trim($_POST['fechaInicioReto']);
    $fechaFin    = trim($_POST['fechaFinReto']);
    $idModulo    = $_POST['modulosReto'] ?? '';

    $errores = [];
    if (empty($nombre)) $errores['nombreReto'] = "El nombre del reto es un campo obligatorio.";
    if (empty($horas)) {
        $errores['horasReto'] = "Las horas del reto son un campo obligatorio.";
    } elseif (!is_numeric($horas)) {
        $errores['horasReto'] = "Las horas deben ser un valor numérico.";
    }

    $hoy = date('Y-m-d');
    if (empty($fechaInicio)) {
        $errores['fechaInicioReto'] = "La fecha de inicio es un campo obligatorio.";
    } elseif ($fechaInicio < $hoy) {
        $errores['fechaInicioReto'] = "La fecha de inicio no puede ser anterior a hoy.";
    }
    if (empty($fechaFin)) {
        $errores['fechaFinReto'] = "La fecha de fin es un campo obligatorio.";
    } elseif (!empty($fechaInicio) && $fechaFin < $fechaInicio) {
        $errores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horas) && is_numeric($horas) && $fechaInicio <= $fechaFin) {
        $maxHoras = calcularMaxHorasLaborables($fechaInicio, $fechaFin);
        if ($horas > $maxHoras) {
            $errores['horasReto'] = "Las horas ($horas h) superan el máximo permitido ($maxHoras h).";
        }
    }

    if (empty($idModulo) || !is_numeric($idModulo)) {
        $errores['modulosReto'] = "Debe seleccionar un módulo para el reto.";
    } elseif (is_numeric($horas)) {
        $detalle = obtenerDetalleHorasModulo($idModulo);
        if ($horas > $detalle['disponibles']) {
            $errores['modulosReto'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/admin/retos/agregarRetos.php");
        exit;
    }

    if (insertarReto($nombre, $fechaInicio, $fechaFin, $horas, [$idModulo])) {
        $idNuevoReto = mysqli_insert_id(obtenerConexion());
        registrarAccion('insertar', 'retos', $idNuevoReto, $nombre);

        procesarArchivosReto($idNuevoReto);

        $_SESSION['exito'] = "El reto ha sido creado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar crear el reto en la base de datos.";
    header("Location: ../../../vistas/admin/retos/agregarRetos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;

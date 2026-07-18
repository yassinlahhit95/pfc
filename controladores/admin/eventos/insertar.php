<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_eventos');
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarEvento'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
        exit;
    }
    $titulo          = trim($_POST['tituloEvento']);
    $descripcion     = trim($_POST['descripcionEvento']);
    $fechaEvento     = trim($_POST['fechaEvento']);
    $horaEvento      = trim($_POST['horaEvento']);
    $ubicacionEvento = trim($_POST['ubicacionEvento']);

    $errores = [];
    if (empty($titulo))          $errores['tituloEvento'] = "El título del evento es un campo obligatorio.";
    if (empty($ubicacionEvento)) $errores['ubicacionEvento'] = "La ubicación del evento es un campo obligatorio.";
    if (empty($fechaEvento))     $errores['fechaEvento'] = "La fecha del evento es un campo obligatorio.";
    if (empty($horaEvento))      $errores['horaEvento'] = "La hora del evento es un campo obligatorio.";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
        header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
        exit;
    }

    if (insertarEvento($titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento)) {
        registrarAccion('insertar', 'eventos', null, $titulo);
        $_SESSION['exito'] = "El evento ha sido creado correctamente.";
        header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar guardar el evento.";
    header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;

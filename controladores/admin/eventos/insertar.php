<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/eventos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarEvento'])) {
    $titulo          = trim($_POST['tituloEvento']);
    $descripcion     = trim($_POST['descripcionEvento']);
    $fechaEvento     = trim($_POST['fechaEvento']);
    $horaEvento      = trim($_POST['horaEvento']);
    $ubicacionEvento = trim($_POST['ubicacionEvento']);

    $errores = '';
    if (empty($titulo))          $errores = "El título del evento es un campo obligatorio.";
    if (empty($ubicacionEvento)) $errores = "La ubicación del evento es un campo obligatorio.";
    if (empty($fechaEvento))     $errores = "La fecha del evento es un campo obligatorio.";
    if (empty($horaEvento))      $errores = "La hora del evento es un campo obligatorio.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
        header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
        exit;
    }

    if (insertarEvento($titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento)) {
        $_SESSION['exito'] = "El evento ha sido publicado correctamente.";
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

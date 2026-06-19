<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/eventos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarEvento'])) {
    $idEvento        = (int)($_POST['idEvento'] ?? 0);
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

    if (!$errores) {
        if (actualizarEvento($idEvento, $titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento)) {
            $_SESSION['exito'] = "El evento ha sido actualizado correctamente.";
            header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el evento.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
    }

    header("Location: ../../../vistas/admin/eventos/modificarEvento.php?idEvento=" . $idEvento);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;

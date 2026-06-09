<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['guardarEvento'])) {
    $titulo = trim($_POST['tituloEvento']);
    $descripcion = trim($_POST['descripcionEvento']);
    $fechaEvento = trim($_POST['fechaEvento']);
    $horaEvento = trim($_POST['horaEvento']);
    $ubicacionEvento = trim($_POST['ubicacionEvento']);

    $errores = '';
    if (empty($titulo)) $errores = "Falta el título";
    if (empty($ubicacionEvento)) $errores = "La ubicación es obligatoria.";
    if (empty($fechaEvento)) $errores = "Fecha requerida";
    if (empty($horaEvento)) $errores = "La hora es obligatoria.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
        header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
        exit;
    }

    if (insertarEvento($titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento)) {
        $_SESSION['exito'] = "Evento publicado.";
        header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo guardar el evento.";
    header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
    exit;
}
?>

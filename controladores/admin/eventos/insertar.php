<?php
session_start();
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['guardarEvento'])) {
    $titulo = trim($_POST['tituloEvento']);
    $descripcion = trim($_POST['descripcionEvento']);
    $fechaEvento = trim($_POST['fechaEvento']);
    $horaEvento = trim($_POST['horaEvento']);
    $ubicacionEvento = trim($_POST['ubicacionEvento']);

    $errores = [];

    if (empty($titulo)) {
        $errores['tituloEvento'] = "Falta el título";
    }
    if (empty($ubicacionEvento)) {
        $errores['ubicacionEvento'] = "La ubicación es obligatoria.";
    }
    if (empty($fechaEvento)) {
        $errores['fechaEvento'] = "Fecha requerida";
    }
    if (empty($horaEvento)) {
        $errores['horaEvento'] = "La hora es obligatoria.";
    }

    if (empty($errores)) {
        $resultado = insertarEvento($titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento);
        if ($resultado) {
            $_SESSION['exito'] = "Evento publicado.";
            header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo guardar el evento.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_evento'] = $_POST;
    }

    header("Location: ../../../vistas/admin/eventos/agregarEvento.php");
    exit;
}
?>

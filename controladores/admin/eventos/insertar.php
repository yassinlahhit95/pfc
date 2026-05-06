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

    if (empty($titulo) || empty($fechaEvento)) {
        $errores['datos'] = "Faltan datos.";
    }

    if (empty($errores)) {
        $resultado = insertarEvento($titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento);
        if ($resultado) {
            $_SESSION['exito'] = "Evento publicado.";
        } else {
            $_SESSION['error'] = "No se pudo guardar el evento.";
        }
    } else {
        $_SESSION['error'] = $errores['datos'];
    }
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;



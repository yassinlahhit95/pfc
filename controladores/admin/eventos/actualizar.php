<?php
session_start();
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['actualizarEvento'])) {
    $idEvento = trim($_POST['idEvento']);
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
        $resultado = actualizarEvento($idEvento, $titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento);
        if ($resultado) {
            $_SESSION['exito'] = "Evento actualizado.";
            header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
            exit;
        }
        $_SESSION['error'] = "Error al actualizar.";
    } else {
        $_SESSION['error'] = $errores['datos'];
    }
    
    header("Location: ../../../vistas/admin/eventos/modificarEvento.php?idEvento=" . $idEvento);
    exit;
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;



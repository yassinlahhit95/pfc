<?php
session_start();
require_once __DIR__ . "/../../../modelos/eventos.php";

if (isset($_POST['actualizarEvento'])) {
    $idEvento = trim($_POST['idEvento'] ?? '');
    $titulo = trim($_POST['tituloEvento'] ?? '');
    $descripcion = trim($_POST['descripcionEvento'] ?? '');
    $fechaEvento = trim($_POST['fechaEvento'] ?? '');
    $horaEvento = trim($_POST['horaEvento'] ?? '');
    $ubicacionEvento = trim($_POST['ubicacionEvento'] ?? '');

    $hayError = false;

    if (empty($titulo) || empty($fechaEvento)) {
        $_SESSION['error'] = "Faltan datos.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = actualizarEvento($idEvento, $titulo, $descripcion, $fechaEvento, $horaEvento, $ubicacionEvento);
        if ($resultado) {
            $_SESSION['exito'] = "Evento actualizado.";
            header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar.";
        }
    }
    
    header("Location: ../../../vistas/admin/eventos/modificarEvento.php?idEvento=" . $idEvento);
    exit;
}

header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;



<?php
session_start();
require_once "../../../modelos/eventos.php";

if (isset($_POST['actualizarEvento'])) {
    $id = $_POST['idEvento'];
    $titulo = trim($_POST['tituloEvento']);
    $descripcion = trim($_POST['descripcionEvento']);
    $fecha = $_POST['fechaEvento'];
    $hora = $_POST['horaEvento'];
    $ubicacion = trim($_POST['ubicacionEvento']);

    if (empty($titulo) || empty($fecha)) {
        $_SESSION['error'] = "El título y la fecha son obligatorios.";
        header("Location: /pfc/vistas/admin/eventos/modificarEvento.php?idEvento=" . $id);
        exit;
    } else {
        $resultado = actualizarEvento($id, $titulo, $descripcion, $fecha, $hora, $ubicacion);
        if ($resultado) {
            $_SESSION['exito'] = "Evento actualizado correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar el evento.";
        }
    }
}

header("Location: /pfc/vistas/admin/eventos/gestionEventos.php");
exit;
?>
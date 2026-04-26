<?php
session_start();
require_once "../../../modelos/eventos.php";

if (isset($_POST['guardarEvento'])) {
    $titulo = trim($_POST['tituloEvento']);
    $descripcion = trim($_POST['descripcionEvento']);
    $fecha = $_POST['fechaEvento'];
    $hora = $_POST['horaEvento'];
    $ubicacion = trim($_POST['ubicacionEvento']);

    if (empty($titulo) || empty($fecha)) {
        $_SESSION['error'] = "El título y la fecha son obligatorios.";
    } else {
        $resultado = insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion);
        if ($resultado) {
            $_SESSION['exito'] = "Evento publicado correctamente.";
        } else {
            $_SESSION['error'] = "Error al guardar el evento.";
        }
    }
}

header("Location: /pfc/vistas/admin/eventos/gestionEventos.php");
exit;
?>

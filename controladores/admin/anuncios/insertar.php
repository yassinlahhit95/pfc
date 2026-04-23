<?php
session_start();
require_once "../../../modelos/anuncios.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['titulo']);
    $mensaje = trim($_POST['mensaje']);
    $fecha = $_POST['fecha_expiracion'];
    $dirigidoA = $_POST['dirigidoA'];

    if (empty($titulo)) {
        $_SESSION['error'] = "El título es obligatorio.";
    } else if (empty($mensaje)) {
        $_SESSION['error'] = "El mensaje es obligatorio.";
    } else if (empty($fecha)) {
        $_SESSION['error'] = "La fecha es obligatoria.";
    } else {
        if (insertarAnuncio($titulo, $mensaje, $fecha, $dirigidoA)) {
            $_SESSION['exito'] = "Anuncio publicado correctamente.";
        } else {
            $_SESSION['error'] = "Error al guardar el anuncio.";
        }
    }
    header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}

header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>
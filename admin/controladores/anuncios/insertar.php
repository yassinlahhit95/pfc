<?php
session_start();
require_once "../../../modelos/anuncios.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['titulo']);
    $mensaje = trim($_POST['mensaje']);
    $fecha = $_POST['fecha_expiracion'];

    if (empty($titulo)) {
        $_SESSION['error'] = "El título es obligatorio.";
        header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    } else if (empty($mensaje)) {
        $_SESSION['error'] = "El mensaje es obligatorio.";
        header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    } else if (empty($fecha)) {
        $_SESSION['error'] = "La fecha de expiración es obligatoria.";
        header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    } else {
        if (insertarAnuncio($titulo, $mensaje, $fecha)) {
            $_SESSION['exito'] = "Anuncio publicado correctamente.";
        } else {
            $_SESSION['error'] = "No se ha podido guardar el anuncio.";
        }
        header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    }
    exit;
}

header("Location: ../../vistas/anuncios/gestionAnuncios.php");
exit;
?>
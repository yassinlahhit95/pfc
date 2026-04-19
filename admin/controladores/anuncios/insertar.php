<?php
session_start();
require_once "../../modelos/anuncios.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $fecha = $_POST['fecha_expiracion'] ?? '';

    $_SESSION['datos_anuncio'] = $_POST;

    if (empty($titulo) || empty($mensaje) || empty($fecha)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: ../../vistas/anuncios/gestionAnuncios.php");
        exit;
    }

    if (insertarNuevoAnuncio($titulo, $mensaje, $fecha)) {
        unset($_SESSION['datos_anuncio']);
        $_SESSION['exito'] = "Anuncio publicado con éxito.";
    } else {
        $_SESSION['error'] = "Error al publicar el anuncio en la base de datos.";
    }
}

header("Location: ../../vistas/anuncios/gestionAnuncios.php");
exit;
?>

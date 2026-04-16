<?php
session_start();
require_once "../../modelos/anuncios.php";

if (isset($_POST['guardarAnuncio'])) {
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_anuncios']);

    $titulo = trim($_POST['titulo'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');
    $fecha_expiracion = trim($_POST['fecha_expiracion'] ?? '');
    $errores = [];

    if (empty($titulo)) {
        $errores['titulo'] = "El título es obligatorio";
    }

    if (empty($mensaje)) {
        $errores['mensaje'] = "El mensaje es obligatorio";
    }

    if (empty($fecha_expiracion)) {
        $errores['fecha_expiracion'] = "La fecha de expiración es obligatoria";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_anuncios'] = $_POST;
        header("Location: ../../vistas/anuncios/gestionAnuncios.php");
        exit;
    }

    $modelo = new anuncio();
    if ($modelo->insertarAnuncioModelo($titulo, $mensaje, $fecha_expiracion)) {
        $_SESSION['exito'] = "Anuncio publicado correctamente";
    } else {
        $_SESSION['error'] = "Error al publicar el anuncio";
    }

    header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    exit;
}
?>

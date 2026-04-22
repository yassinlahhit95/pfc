<?php
session_start();
require_once "../../../modelos/anuncios.php";
if (isset($_POST['guardarAnuncio'])) {
    $titulo = $_POST['titulo'];
    $mensaje = $_POST['mensaje'];
    $fecha = $_POST['fecha_expiracion'];
    if (empty($titulo)) {
        $_SESSION['error'] = "Título obligatorio";
    } else if (empty($mensaje)) {
        $_SESSION['error'] = "Mensaje obligatorio";
    } else if (empty($fecha)) {
        $_SESSION['error'] = "Fecha obligatoria";
    } else if (insertarAnuncio($titulo, $mensaje, $fecha)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
    exit;
}
header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
exit;


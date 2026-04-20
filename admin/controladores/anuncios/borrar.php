<?php
session_start();
require_once "../../../modelos/anuncios.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "El identificador del anuncio es obligatorio.";
    header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    exit;
}

// 1. Normalización de datos
$idDelAnuncio = trim($_GET['id']);

// 2. Validación estricta
if (!is_numeric($idDelAnuncio)) {
    $_SESSION['error'] = "El identificador del anuncio debe ser un número.";
    header("Location: ../../vistas/anuncios/gestionAnuncios.php");
    exit;
}

// 3. Modelo funcional simple
if (borrarAnuncioPorId($idDelAnuncio)) {
    $_SESSION['mensaje'] = "Anuncio borrado con éxito.";
} else {
    $_SESSION['error'] = "No se pudo borrar el anuncio.";
}

header("Location: ../../vistas/anuncios/gestionAnuncios.php");
exit;
?>

<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

if (isset($_POST['actualizarAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    $hayError = false;

    if (empty($titulo)) {
        $hayError = true;
        $_SESSION['error'] = "El título es obligatorio.";
    } elseif (empty($contenido)) {
        $hayError = true;
        $_SESSION['error'] = "El contenido es obligatorio.";
    }

    if (!$hayError) {
        $anuncioActual = obtenerAnuncioPorId($idAnuncio);
        $fechaExpiracion = $anuncioActual['fechaExpiracion'];
        $dirigidoA = $anuncioActual['dirigidoA'];
        $resultado = actualizarAnuncio($idAnuncio, $titulo, $contenido, $fechaExpiracion, $dirigidoA);
        
        if ($resultado) {
            $_SESSION['exito'] = "Anuncio actualizado.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=$idAnuncio");
    exit;
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;

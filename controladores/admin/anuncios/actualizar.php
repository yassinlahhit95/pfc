<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

if (isset($_POST['actualizarAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    $hayError = false;

    $listaErrores = [];
    if (empty($titulo)) {
        $hayError = true;
        $listaErrores['tituloAnuncio'] = "El título es obligatorio.";
    }
    if (empty($contenido)) {
        $hayError = true;
        $listaErrores['contenidoAnuncio'] = "El contenido es obligatorio.";
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
            $_SESSION['error'] = "Error inesperado al actualizar.";
        }
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=$idAnuncio");
    exit;
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;



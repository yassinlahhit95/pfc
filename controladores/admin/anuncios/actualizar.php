<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

if (isset($_POST['actualizarAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    $listaErrores = [];

    if (empty($titulo)) {
        $listaErrores['tituloAnuncio'] = "El título es obligatorio.";
    }

    if (empty($contenido)) {
        $listaErrores['contenidoAnuncio'] = "El contenido es obligatorio.";
    }

    if (empty($listaErrores)) {
        $anuncioActual = obtenerAnuncioPorId($idAnuncio);
        $fechaExpiracion = $anuncioActual['fechaExpiracion'];
        $dirigidoA = $anuncioActual['dirigidoA'];

        $resultado = actualizarAnuncio($idAnuncio, $titulo, $contenido, $fechaExpiracion, $dirigidoA);

        if ($resultado) {
            $_SESSION['exito'] = "Anuncio actualizado correctamente.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        }

        $_SESSION['error'] = "Error inesperado al actualizar el anuncio.";
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=$idAnuncio");
    exit;
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>

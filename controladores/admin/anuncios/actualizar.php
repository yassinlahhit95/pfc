<?php
session_start();
require_once "../../../modelos/anuncios.php";

if (isset($_POST['actualizarAnuncio'])) {
    $id_anuncio = $_POST['idAnuncio'];
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    $lista_de_errores = [];

    if (empty($titulo)) {
        $lista_de_errores['tituloAnuncio'] = "El título es obligatorio.";
    }
    if (empty($contenido)) {
        $lista_de_errores['contenidoAnuncio'] = "El contenido es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarAnuncio($id_anuncio, $titulo, $contenido);
        if ($resultado) {
            $_SESSION['exito'] = "Anuncio actualizado correctamente.";
            header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=$id_anuncio");
    exit;
}

header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
exit;

<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../firebase/firebase_helper.php";

if (isset($_POST['guardarAnuncio'])) {
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);
    $dirigidoA = trim($_POST['dirigidoA']);

    $hayError = false;

    $listaErrores = [];
    if (empty($titulo)) {
        $listaErrores['tituloAnuncio'] = "El título es obligatorio.";
        $hayError = true;
    }
    if (empty($contenido)) {
        $listaErrores['contenidoAnuncio'] = "El contenido es obligatorio.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = insertarAnuncio($titulo, $contenido, $dirigidoA);
        
        if ($resultado) {
            // ... rest of tokens and push logic ...
            $_SESSION['exito'] = "Anuncio publicado y notificado.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        } else {
            $_SESSION['error'] = "Error inesperado al guardar.";
        }
    } else {
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    header("Location: ../../../vistas/admin/anuncios/agregarAnuncios.php");
    exit;
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;



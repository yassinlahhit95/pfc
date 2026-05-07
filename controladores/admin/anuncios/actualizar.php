<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

// Verificamos si se ha enviado el formulario de actualización
if (isset($_POST['actualizarAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);
    $titulo = trim($_POST['tituloAnuncio']);
    $contenido = trim($_POST['contenidoAnuncio']);

    $listaErrores = [];
    
    // Validación: El título no puede estar vacío
    if (empty($titulo)) {
        $listaErrores['tituloAnuncio'] = "El título es obligatorio.";
    }
    
    // Validación: El contenido no puede estar vacío
    if (empty($contenido)) {
        $listaErrores['contenidoAnuncio'] = "El contenido es obligatorio.";
    }

    // Si no hay errores de validación, procedemos con la actualización
    if (empty($listaErrores)) {
        // Recuperamos los datos actuales del anuncio para mantener la fecha de expiración y a quién va dirigido
        $anuncioActual = obtenerAnuncioPorId($idAnuncio);
        $fechaExpiracion = $anuncioActual['fechaExpiracion'];
        $dirigidoA = $anuncioActual['dirigidoA'];
        
        // Intentamos actualizar el registro en la base de datos
        $resultado = actualizarAnuncio($idAnuncio, $titulo, $contenido, $fechaExpiracion, $dirigidoA);

        if ($resultado) {
            $_SESSION['exito'] = "Anuncio actualizado correctamente.";
            header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
            exit;
        }
        
        // En caso de error en la base de datos
        $_SESSION['error'] = "Error inesperado al actualizar el anuncio.";
    } else {
        // Si hay errores de validación, los guardamos en la sesión para mostrarlos en el formulario
        $_SESSION['errores'] = $listaErrores;
        $_SESSION['datos_anuncio'] = $_POST;
    }

    // Redireccionamos de vuelta al formulario de modificación si algo falló
    header("Location: ../../../vistas/admin/anuncios/modificarAnuncios.php?idAnuncio=$idAnuncio");
    exit;
}

// Redirección por defecto a la gestión de anuncios si se accede directamente
header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>

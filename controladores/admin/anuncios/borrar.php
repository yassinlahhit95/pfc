<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

// Verificamos si se ha recibido el identificador del anuncio a eliminar
if (isset($_POST['idAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);
    
    // Intentamos eliminar el anuncio de la base de datos
    if (eliminarAnuncio($idAnuncio)) {
        $_SESSION['exito'] = "Anuncio eliminado correctamente.";
    } else {
        // Si la operación falla, informamos al usuario
        $_SESSION['error'] = "No se ha podido eliminar el anuncio seleccionado.";
    }
}

// Redireccionamos a la pantalla de gestión de anuncios
header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>

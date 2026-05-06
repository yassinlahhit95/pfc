<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

if (isset($_POST['idAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);
    
    if (eliminarAnuncio($idAnuncio)) {
        $_SESSION['exito'] = "Anuncio eliminado.";
    } else {
        $_SESSION['error'] = "Error al eliminar el anuncio.";
    }
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>

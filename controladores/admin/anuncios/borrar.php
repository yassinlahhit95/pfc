<?php
session_start();
require_once "../../../modelos/anuncios.php";

if (isset($_POST['idAnuncio'])) {
    $id = $_POST['idAnuncio'];
    if (eliminarAnuncio($id)) {
        $_SESSION['exito'] = "Anuncio eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el anuncio.";
    }
}
header("Location: /pfc/vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>


<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";

if (isset($_POST['idAnuncio'])) {
    $idAnuncio = (int)($_POST['idAnuncio'] ?? 0);

    if (eliminarAnuncio($idAnuncio)) {
        $_SESSION['exito'] = "El anuncio ha sido eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el anuncio seleccionado.";
    }
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>

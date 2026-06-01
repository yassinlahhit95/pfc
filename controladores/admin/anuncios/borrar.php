<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../include/Security.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if (isset($_POST['idAnuncio'])) {
    $idAnuncio = trim($_POST['idAnuncio']);

    if (eliminarAnuncio($idAnuncio)) {
        $_SESSION['exito'] = "Anuncio eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "No se ha podido eliminar el anuncio seleccionado.";
    }
}

header("Location: ../../../vistas/admin/anuncios/gestionAnuncios.php");
exit;
?>

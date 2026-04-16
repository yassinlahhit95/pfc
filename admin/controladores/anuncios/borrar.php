<?php
session_start();
require_once "../../modelos/anuncios.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Numeric validation (from standards)
    if (!is_numeric($id) || !ctype_digit((string)$id) || !preg_match('/^[0-9]+$/', (string)$id)) {
        $_SESSION['error'] = "ID de anuncio no válido";
    } else {
        $modelo = new anuncio();
        if ($modelo->eliminarAnuncioModelo($id)) {
            $_SESSION['exito'] = "Anuncio borrado correctamente";
        } else {
            $_SESSION['error'] = "Error al borrar el anuncio";
        }
    }
}

header("Location: ../../vistas/anuncios/gestionAnuncios.php");
exit;
?>

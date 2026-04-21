<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['idReto'])) {
    $idReto = $_POST['idReto'];

    if (ctype_digit($idReto)) {
        if (eliminarReto($idReto)) {
            $_SESSION['mensaje'] = "Reto borrado con éxito.";
        } else {
            $_SESSION['error'] = "No se ha podido borrar el reto.";
        }
    } else {
        $_SESSION['error'] = "ID de reto no válido.";
    }
}

header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;
?>

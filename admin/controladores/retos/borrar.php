<?php
session_start();
require_once "../../modelos/retos.php";

if (isset($_POST['idReto'])) {
    $id = $_POST['idReto'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new reto();
        if ($modelo->eliminarRetoModelo($id)) {
            $_SESSION['exito'] = "Reto borrado correctamente";
        } else {
            $_SESSION['error'] = "Error al borrar el reto";
        }
    } else {
        $_SESSION['error'] = "ID del reto no válido";
    }
}

header("Location: ../../vistas/retos/verRetos.php");
exit;
?>

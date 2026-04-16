<?php
session_start();
require_once "../../modelos/directores.php";

if (isset($_POST['idDirector'])) {
    $id = $_POST['idDirector'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new director();
        if ($modelo->eliminarDirectoresModelo($id)) {
            $_SESSION['exito'] = "Director eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el director";
        }
    } else {
        $_SESSION['error'] = "ID del director no válido";
    }
}

header("Location: ../../vistas/directores/verDirectores.php");
exit;
?>

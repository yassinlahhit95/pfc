<?php
session_start();
require_once "../../modelos/modulos.php";

if (isset($_POST['idModulo'])) {
    $id = $_POST['idModulo'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new modulo();
        if ($modelo->eliminarModuloModelo($id)) {
            $_SESSION['exito'] = "Módulo borrado correctamente";
        } else {
            $_SESSION['error'] = "Error al borrar el módulo";
        }
    } else {
        $_SESSION['error'] = "ID de módulo no válido";
    }
}

header("Location: ../../vistas/modulos/verModulos.php");
exit;
?>

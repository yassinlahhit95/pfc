<?php
session_start();
require_once "../../modelos/modulos.php";

if (isset($_POST['idModulo'])) {
    $idDelModulo = $_POST['idModulo'];
    
    if (empty($idDelModulo) || !ctype_digit($idDelModulo)) {
        $_SESSION['error'] = "ID de módulo no válido.";
        header("Location: ../../vistas/modulos/verModulos.php");
        exit;
    }

    if (eliminarModulo($idDelModulo)) {
        $_SESSION['mensaje'] = "Módulo eliminado con éxito.";
    } else {
        $_SESSION['error'] = "No se ha podido eliminar el módulo.";
    }
}

header("Location: ../../vistas/modulos/verModulos.php");
exit;
?>

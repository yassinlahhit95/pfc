<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['idReto'])) {
    $idReto = (int)$_POST['idReto'];
    
    if (eliminarReto($idReto)) {
        $_SESSION['exito'] = "Reto eliminado.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Error al eliminar el reto.";
    }
} else {
    $hayError = true;
    $_SESSION['errores'] = "No se especificó el reto.";
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
?>

<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['idDirector'])) {
    $idDirector = trim($_POST['idDirector']);
    
    if (eliminarDirector($idDirector)) {
        $_SESSION['exito'] = "Director eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "Error al eliminar el director.";
    }
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>

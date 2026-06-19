<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['idDirector'])) {
    $idDirector = (int)($_POST['idDirector'] ?? 0);
    
    if (eliminarDirector($idDirector)) {
        $_SESSION['exito'] = "El director ha sido eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el director.";
    }
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>

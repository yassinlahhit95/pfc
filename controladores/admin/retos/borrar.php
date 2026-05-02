<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['idReto'])) {
    $idReto = trim($_POST['idReto']);
    
    if (eliminarReto($idReto)) {
        $_SESSION['exito'] = "Listo! El reto ha sido eliminado.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, no se pudo eliminar el reto.";
    }
} else {
    $hayError = true;
    $_SESSION['error'] = "Vaya, no se especificÃ³ quÃ© reto borrar.";
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;

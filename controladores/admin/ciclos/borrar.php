<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

if (isset($_POST['idCiclo'])) {
    $idCiclo = trim($_POST['idCiclo']);
    
    if (eliminarCiclo($idCiclo)) {
        $_SESSION['exito'] = "Ciclo eliminado.";
    } else {
        $_SESSION['error'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/ciclos/verCiclos.php");
exit;
?>

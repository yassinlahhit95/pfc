<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_GET['id'])) {
    $idReto = trim($_GET['id']);
    
    $resultado = eliminarReto($idReto);
    if ($resultado) {
        $_SESSION['exito'] = "Listo! Reto eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Vaya, no se ha podido eliminar el reto.";
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
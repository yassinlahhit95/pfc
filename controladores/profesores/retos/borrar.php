<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_GET['id'])) {
    $idReto = trim($_GET['id']);
    
    $resultado = eliminarReto($idReto);
    if ($resultado) {
        $_SESSION['exito'] = "Reto eliminado.";
    } else {
        $_SESSION['error'] = "Error al eliminar el reto.";
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
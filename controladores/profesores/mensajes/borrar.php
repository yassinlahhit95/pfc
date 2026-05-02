<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if (isset($_GET['id'])) {
    $idReclamacion = trim($_GET['id']);
    
    if (eliminarMensaje($idReclamacion)) {
        $_SESSION['exito'] = "Listo! El mensaje ha sido eliminado.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, no se pudo eliminar el mensaje.";
    }
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;

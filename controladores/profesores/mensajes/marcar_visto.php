<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if (isset($_POST['marcarVisto'])) {
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje visto.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Error al actualizar.";
    }
    
    header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
?>

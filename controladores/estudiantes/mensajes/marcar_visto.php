<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (isset($_POST['marcarVisto'])) {
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje marcado como leído.";
    } else {
        $_SESSION['error'] = "Error al actualizar el estado.";
    }
    
    header("Location: ../../../vistas/estudiantes/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
?>
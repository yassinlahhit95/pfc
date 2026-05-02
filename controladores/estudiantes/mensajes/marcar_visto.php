<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (isset($_POST['marcarVisto'])) {
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = strtoupper("MENSAJE MARCADO COMO LEÃDO.");
    } else {
        $_SESSION['error'] = strtoupper("ERROR AL ACTUALIZAR EL ESTADO DEL MENSAJE.");
    }
    
    header("Location: ../../../vistas/estudiantes/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
exit;
?>

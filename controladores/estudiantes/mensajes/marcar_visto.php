<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['marcarVisto'])) {
    $id = $_POST['idReclamacion'];
    
    if (marcarMensajeComoLeido($id)) {
        $_SESSION['exito'] = strtoupper("MENSAJE MARCADO COMO LEÍDO.");
    } else {
        $_SESSION['error'] = strtoupper("ERROR AL ACTUALIZAR EL ESTADO DEL MENSAJE.");
    }
    
    header("Location: /pfc/vistas/estudiantes/mensajes/detalles.php?id=" . $id);
    exit;
}

header("Location: /pfc/vistas/estudiantes/mensajes/lista.php");
exit;
?>

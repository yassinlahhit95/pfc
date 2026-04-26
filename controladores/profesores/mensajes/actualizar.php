<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['idReclamacion'])) {
    $idReclamacion = $_POST['idReclamacion'];
    
    if (isset($_POST['guardarRespuesta'])) {
        $respuesta = trim($_POST['respuesta']);
        if (responderMensaje($idReclamacion, $respuesta)) {
            $_SESSION['exito'] = "Respuesta guardada correctamente.";
        } else {
            $_SESSION['error'] = "Error al guardar la respuesta.";
        }
    } else if (isset($_POST['marcarLeido'])) {
        if (marcarMensajeComoLeido($idReclamacion)) {
            $_SESSION['exito'] = "Mensaje marcado como leído.";
        } else {
            $_SESSION['error'] = "Error al actualizar estado.";
        }
    }
}

header("Location: /pfc/vistas/profesores/mensajes/lista.php");
exit;
?>

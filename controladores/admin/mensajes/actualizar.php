<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['idReclamacion'])) {
    $idReclamacion = $_POST['idReclamacion'];
    
    if (isset($_POST['guardarCambios'])) {
        $respuesta = trim($_POST['respuesta']);
        if (responderMensaje($idReclamacion, $respuesta)) {
            $_SESSION['exito'] = "Cambios guardados correctamente.";
        } else {
            $_SESSION['error'] = "Error al guardar los cambios.";
        }
    } else if (isset($_POST['marcarLeido'])) {
        if (marcarMensajeComoLeido($idReclamacion)) {
            $_SESSION['exito'] = "Mensaje marcado como revisado.";
        } else {
            $_SESSION['error'] = "Error al actualizar estado.";
        }
    }
}

header("Location: /pfc/vistas/admin/mensajes/lista.php");
exit;
?>
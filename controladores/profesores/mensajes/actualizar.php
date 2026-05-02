<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if (isset($_POST['idReclamacion'])) {
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (isset($_POST['guardarRespuesta'])) {
        $respuesta = trim($_POST['respuesta'] ?? '');
        if (responderMensaje($idReclamacion, $respuesta)) {
            $_SESSION['exito'] = "Listo! La respuesta ha sido guardada.";
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, no se pudo guardar la respuesta.";
        }
    } else if (isset($_POST['marcarLeido'])) {
        if (marcarMensajeComoLeido($idReclamacion)) {
            $_SESSION['exito'] = "Listo! Mensaje marcado como leÃ­do.";
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, no se pudo actualizar el estado.";
        }
    }
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;

<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if (isset($_POST['idReclamacion'])) {
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (isset($_POST['guardarRespuesta'])) {
        $respuesta = trim($_POST['respuesta']);
        if (responderMensaje($idReclamacion, $respuesta)) {
            $_SESSION['exito'] = "Respuesta guardada.";
        } else {
            $hayError = true;
            $_SESSION['errores'] = "Error al guardar.";
        }
    } else if (isset($_POST['marcarLeido'])) {
        if (marcarMensajeComoLeido($idReclamacion)) {
            $_SESSION['exito'] = "Mensaje leído.";
        } else {
            $hayError = true;
            $_SESSION['errores'] = "Error al actualizar.";
        }
    }
}

header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;
?>

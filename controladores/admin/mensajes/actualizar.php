<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idReclamacion'])) {
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (isset($_POST['guardarCambios'])) {
        $respuestaAdmin = trim($_POST['respuesta']);
        if (responderMensaje($idReclamacion, $respuestaAdmin)) {
            $_SESSION['exito'] = "Listo! Respuesta guardada con Ã©xito.";
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, no pudimos guardar la respuesta.";
        }
    } else if (isset($_POST['marcarLeido'])) {
        if (marcarMensajeComoLeido($idReclamacion)) {
            $_SESSION['exito'] = "Listo! El mensaje ha sido marcado como revisado.";
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, hubo un problema al actualizar el estado.";
        }
    }
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;

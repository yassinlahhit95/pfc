<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (empty($_SESSION['idAdmin'])) { header("Location: ../../../vistas/login.php"); exit; }

if (isset($_POST['idReclamacion'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada.";
        header("Location: ../../../vistas/admin/mensajes/lista.php"); exit;
    }
    $idReclamacion = trim($_POST['idReclamacion']);
    
    if (isset($_POST['guardarCambios'])) {
        $respuestaAdmin = trim($_POST['respuesta']);
        if (responderMensaje($idReclamacion, $respuestaAdmin)) {
            $_SESSION['exito'] = "Respuesta guardada.";
        } else {
            $hayError = true;
            $_SESSION['errores'] = "Error al guardar.";
        }
    } else if (isset($_POST['marcarLeido'])) {
        if (marcarMensajeComoLeido($idReclamacion)) {
            $_SESSION['exito'] = "Mensaje revisado.";
        } else {
            $hayError = true;
            $_SESSION['errores'] = "Error al actualizar.";
        }
    }
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;
?>

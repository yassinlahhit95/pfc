<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

if (!isset($_POST['idReclamacion'])) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)$_POST['idReclamacion'];

if ($idReclamacion <= 0) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

if (!mensajePerteneceAProfesor($idReclamacion, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

if (isset($_POST['guardarRespuesta'])) {
    $respuesta = trim($_POST['respuesta'] ?? '');
    if ($respuesta === '') {
        $_SESSION['errores'] = "El mensaje no puede estar vacío.";
    } elseif (insertarRespuestaMensaje($idReclamacion, null, (int)$_SESSION['idProfesor'], $respuesta, 'profesor')) {
        $_SESSION['exito'] = "Respuesta enviada correctamente.";
    } else {
        $_SESSION['errores'] = "Error al enviar la respuesta.";
    }
} elseif (isset($_POST['marcarLeido'])) {
    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje marcado como leído.";
    } else {
        $_SESSION['errores'] = "Error al actualizar.";
    }
}

header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
exit;

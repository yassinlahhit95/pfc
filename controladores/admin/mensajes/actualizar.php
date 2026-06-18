<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (!isset($_POST['idReclamacion'])) {
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)$_POST['idReclamacion'];

if ($idReclamacion <= 0) {
    $_SESSION['errores'] = "Mensaje no válido.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (isset($_POST['guardarCambios'])) {
    $respuesta = trim($_POST['respuesta'] ?? '');
    if ($respuesta === '') {
        $_SESSION['errores'] = "El mensaje no puede estar vacío.";
    } elseif (insertarRespuestaMensaje($idReclamacion, null, null, $respuesta, 'admin')) {
        $_SESSION['exito'] = "Respuesta enviada correctamente.";
    } else {
        $_SESSION['errores'] = "Error al enviar la respuesta.";
    }
} elseif (isset($_POST['marcarLeido'])) {
    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje marcado como leído.";
    } else {
        $_SESSION['errores'] = "Error al actualizar el estado.";
    }
}

header("Location: ../../../vistas/admin/mensajes/detalles.php?id=" . $idReclamacion);
exit;

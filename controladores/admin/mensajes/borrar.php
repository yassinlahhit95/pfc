<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

if ($idReclamacion <= 0) {
    $_SESSION['errores'] = "Mensaje no válido.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// Verificar que el mensaje existe antes de borrar
$mensaje = obtenerMensajePorId($idReclamacion);
if (!$mensaje) {
    $_SESSION['errores'] = "Mensaje no encontrado.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (eliminarMensaje($idReclamacion)) {
    $_SESSION['exito'] = "Mensaje eliminado correctamente.";
} else {
    $_SESSION['errores'] = "Error al eliminar el mensaje.";
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;

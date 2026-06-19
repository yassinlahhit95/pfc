<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

if ($idReclamacion <= 0) {
    $_SESSION['errores'] = "El identificador del mensaje no es válido.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$mensaje = obtenerMensajePorId($idReclamacion);
if (!$mensaje) {
    $_SESSION['errores'] = "El mensaje especificado no existe.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (eliminarMensaje($idReclamacion)) {
    $_SESSION['exito'] = "El mensaje ha sido eliminado correctamente.";
} else {
    $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el mensaje.";
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;

<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['idReclamacion'])) {
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idReclamacion = (int)$_POST['idReclamacion'];

if ($idReclamacion <= 0) {
    $_SESSION['errores'] = "El identificador del mensaje no es válido.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (isset($_POST['guardarCambios'])) {
    $respuesta = trim($_POST['respuesta'] ?? '');
    if ($respuesta === '') {
        $_SESSION['errores'] = "El contenido de la respuesta no puede estar vacío.";
    } elseif (insertarRespuestaMensaje($idReclamacion, null, null, $respuesta, 'admin')) {
        $_SESSION['exito'] = "La respuesta ha sido enviada correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar enviar la respuesta.";
    }
} elseif (isset($_POST['marcarLeido'])) {
    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "El mensaje ha sido marcado como leído.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el estado del mensaje.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/mensajes/detalles.php?id=" . $idReclamacion);
exit;

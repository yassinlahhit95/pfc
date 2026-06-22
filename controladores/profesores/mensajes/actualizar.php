<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

if (!isset($_POST['idReclamacion'])) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
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

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
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
        $_SESSION['errores'] = "No se pudo actualizar el mensaje.";
    }
}

header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
exit;

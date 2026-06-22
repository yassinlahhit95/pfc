<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['marcarVisto'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada.";
        header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
    }
    $idReclamacion = intval($_POST['idReclamacion'] ?? 0);

    // Solo puede marcar como leído un mensaje dirigido a este profesor (evita IDOR)
    if (!mensajePerteneceAProfesor($idReclamacion, $_SESSION['idProfesor'])) {
        $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
        header("Location: ../../../vistas/profesores/mensajes/lista.php"); exit;
    }

    if (marcarMensajeComoLeido($idReclamacion)) {
        $_SESSION['exito'] = "Mensaje marcado como leído.";
    } else {
        $_SESSION['errores'] = "No se pudo marcar el mensaje como leído.";
    }
    header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/mensajes/lista.php");
exit;

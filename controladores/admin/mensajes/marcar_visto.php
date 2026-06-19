<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../include/Logger.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['marcarVisto'])) {
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($idReclamacion <= 0 || !obtenerMensajePorId($idReclamacion)) {
    $_SESSION['errores'] = "El mensaje no existe o ya fue eliminado.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (marcarMensajeComoLeido($idReclamacion)) {
    $_SESSION['exito'] = "El mensaje ha sido marcado como visto.";
    Logger::activity('MENSAJE_MARCADO_VISTO', $_SESSION['idAdmin'], ['idReclamacion' => $idReclamacion]);
} else {
    $_SESSION['errores'] = "No se pudo marcar el mensaje como visto.";
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;

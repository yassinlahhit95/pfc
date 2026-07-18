<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../include/Logger.php";

$folder      = $_POST['folder'] ?? '';
$queryString = ($folder && $folder !== 'todo') ? '?folder=' . urlencode($folder) : '';
$urlLista    = "../../../vistas/admin/mensajes/lista.php" . $queryString;

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['marcarVisto'])) {
    header("Location: $urlLista");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: $urlLista");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($idReclamacion <= 0 || !obtenerMensajePorId($idReclamacion)) {
    $_SESSION['errores'] = "El mensaje no existe o ya fue eliminado.";
    header("Location: $urlLista");
    exit;
}

if (marcarMensajeComoLeido($idReclamacion)) {
    $_SESSION['exito'] = "El mensaje ha sido marcado como leído.";
    Logger::activity('MENSAJE_MARCADO_VISTO', $_SESSION['idAdmin'], ['idReclamacion' => $idReclamacion]);
} else {
    $_SESSION['errores'] = "No se pudo marcar el mensaje como visto.";
}

header("Location: $urlLista");
exit;

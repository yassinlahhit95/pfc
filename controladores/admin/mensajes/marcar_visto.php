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
    $f = $_POST['folder'] ?? '';
    $q = $f && $f !== 'todo' ? '?folder=' . urlencode($f) : '';
    header("Location: ../../../vistas/admin/mensajes/lista.php" . $q);
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    $f = $_POST['folder'] ?? '';
    $q = $f && $f !== 'todo' ? '?folder=' . urlencode($f) : '';
    header("Location: ../../../vistas/admin/mensajes/lista.php" . $q);
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($idReclamacion <= 0 || !obtenerMensajePorId($idReclamacion)) {
    $_SESSION['errores'] = "El mensaje no existe o ya fue eliminado.";
    $f = $_POST['folder'] ?? '';
    $q = $f && $f !== 'todo' ? '?folder=' . urlencode($f) : '';
    header("Location: ../../../vistas/admin/mensajes/lista.php" . $q);
    exit;
}

if (marcarMensajeComoLeido($idReclamacion)) {
    $_SESSION['exito'] = "El mensaje ha sido marcado como visto.";
    Logger::activity('MENSAJE_MARCADO_VISTO', $_SESSION['idAdmin'], ['idReclamacion' => $idReclamacion]);
} else {
    $_SESSION['errores'] = "No se pudo marcar el mensaje como visto.";
}

$f = $_POST['folder'] ?? '';
$q = $f && $f !== 'todo' ? '?folder=' . urlencode($f) : '';
header("Location: ../../../vistas/admin/mensajes/lista.php" . $q);
exit;

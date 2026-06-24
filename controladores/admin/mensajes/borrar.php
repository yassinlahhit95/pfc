<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_mensajes')) { http_response_code(403); echo json_encode(['error' => 'Módulo desactivado']); exit; }
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!Security::validateCSRFToken()) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida o expirada.']); exit; }
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);

if ($idReclamacion <= 0) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Identificador no válido.']); exit; }
    $_SESSION['errores'] = "El identificador del mensaje no es válido.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$mensaje = obtenerMensajePorId($idReclamacion);
if (!$mensaje) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Mensaje no encontrado.']); exit; }
    $_SESSION['errores'] = "El mensaje especificado no existe.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (eliminarMensaje($idReclamacion)) {
    registrarAccion('borrar', 'reclamaciones', $idReclamacion);
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => true, 'msg' => 'Mensaje eliminado correctamente.']); exit; }
    $_SESSION['exito'] = "El mensaje ha sido eliminado correctamente.";
} else {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Error al eliminar el mensaje.']); exit; }
    $_SESSION['errores'] = "Ocurrió un error al intentar eliminar el mensaje.";
}

header("Location: ../../../vistas/admin/mensajes/lista.php");
exit;

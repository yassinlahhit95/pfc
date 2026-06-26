<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['idReclamacion'])) {
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
       && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud no válida o expirada.']); exit; }
    $_SESSION['errores'] = "Solicitud no válida o expirada.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idReclamacion = (int)$_POST['idReclamacion'];

if ($idReclamacion <= 0) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Identificador no válido.']); exit; }
    $_SESSION['errores'] = "El identificador del mensaje no es válido.";
    header("Location: ../../../vistas/admin/mensajes/lista.php");
    exit;
}

if (isset($_POST['guardarCambios'])) {
    $respuesta = trim($_POST['respuesta'] ?? '');
    if ($respuesta === '') {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'La respuesta no puede estar vacía.']); exit; }
        $_SESSION['errores'] = ['respuesta' => "El contenido de la respuesta no puede estar vacío."];
        header("Location: ../../../vistas/admin/mensajes/modificarReclamacion.php?idReclamacion=" . $idReclamacion);
        exit;
    } elseif (insertarRespuestaMensaje($idReclamacion, null, null, $respuesta, 'admin')) {
        registrarAccion('responder', 'reclamaciones', $idReclamacion);
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => true]); exit; }
        $_SESSION['exito'] = "La respuesta ha sido enviada correctamente.";
    } else {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Error al enviar la respuesta.']); exit; }
        $_SESSION['errores'] = "Ocurrió un error al intentar enviar la respuesta.";
    }
} elseif (isset($_POST['marcarLeido'])) {
    if (marcarMensajeComoLeido($idReclamacion)) {
        registrarAccion('marcar_leido', 'reclamaciones', $idReclamacion);
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => true]); exit; }
        $_SESSION['exito'] = "El mensaje ha sido marcado como leído.";
    } else {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Error al actualizar el estado.']); exit; }
        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el estado del mensaje.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/mensajes/detalles.php?id=" . $idReclamacion);
exit;

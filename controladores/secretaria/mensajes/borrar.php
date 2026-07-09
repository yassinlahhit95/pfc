<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!Security::validateCSRFToken()) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    $_SESSION['errores'] = "Solicitud inválida.";
    header("Location: ../../../vistas/secretaria/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);
if ($idReclamacion <= 0) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Identificador no válido.']); exit; }
    header("Location: ../../../vistas/secretaria/mensajes/lista.php");
    exit;
}

if (eliminarMensaje($idReclamacion)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => true, 'msg' => 'Mensaje eliminado.']); exit; }
    $_SESSION['exito'] = "Mensaje eliminado.";
} else {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Error al eliminar.']); exit; }
    $_SESSION['errores'] = "No se pudo eliminar el mensaje.";
}

header("Location: ../../../vistas/secretaria/mensajes/lista.php");
exit;

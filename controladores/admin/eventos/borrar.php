<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_eventos')) { http_response_code(403); echo json_encode(['error' => 'Módulo desactivado']); exit; }
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/eventos/gestionEventos.php"); exit;
}

if (isset($_POST['idEvento'])) {
    $idEvento = (int)($_POST['idEvento'] ?? 0);
    if (eliminarEvento($idEvento)) {
        registrarAccion('borrar', 'eventos', $idEvento);
        $ok = true; $msg = "Evento eliminado.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "No se pudo eliminar el evento.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/eventos/gestionEventos.php");
exit;
?>

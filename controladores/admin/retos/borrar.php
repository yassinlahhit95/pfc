<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_retos')) { http_response_code(403); echo json_encode(['error' => 'Módulo desactivado']); exit; }
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/retos/verRetos.php"); exit;
}

if (isset($_POST['idReto'])) {
    $idReto = (int)$_POST['idReto'];
    if (eliminarReto($idReto)) {
        registrarAccion('borrar', 'retos', $idReto);
        $ok = true; $msg = "Reto eliminado.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Error al eliminar el reto.";
        $_SESSION['errores'] = $msg;
    }
} else {
    $msg = "No se especificó el reto.";
    $_SESSION['errores'] = $msg;
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
?>

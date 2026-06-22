<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/rgpd.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']); exit; }
    header("Location: ../../../vistas/admin/rgpd/index.php"); exit;
}

if (!Security::validateCSRFToken(null, false)) {
    $msg = 'Solicitud inválida.';
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => $msg]); exit; }
    $_SESSION['errores'] = $msg;
    header("Location: ../../../vistas/admin/rgpd/index.php"); exit;
}

$years = max(3, (int)($_POST['years'] ?? 3));

$eliminados = purgarLogsAntiguos($years);
registrarAccion('purgar_logs', 'log_acciones', null, "Eliminados $eliminados registros de más de $years años");

$msg = $eliminados > 0
    ? "Se han eliminado $eliminados registros del log de actividad de más de $years años (LOPDGDD)."
    : "No había registros de log con más de $years años de antigüedad.";

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'msg' => $msg, 'eliminados' => $eliminados]);
    exit;
}

$_SESSION['exito'] = $msg;
header("Location: ../../../vistas/admin/rgpd/index.php");
exit;

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

$idEstudiante  = (int)($_POST['idEstudiante'] ?? 0);
$motivo        = trim($_POST['motivo'] ?? '');
$adminPassword = $_POST['adminPassword'] ?? '';

if ($idEstudiante <= 0) {
    $msg = 'ID de estudiante no válido.';
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => $msg]); exit; }
    $_SESSION['errores'] = $msg;
    header("Location: ../../../vistas/admin/rgpd/index.php"); exit;
}

if (empty($motivo)) {
    $msg = 'El motivo de la eliminación es obligatorio (RGPD Art. 17).';
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => $msg]); exit; }
    $_SESSION['errores'] = $msg;
    header("Location: ../../../vistas/admin/rgpd/index.php"); exit;
}

if (empty($adminPassword)) {
    $msg = 'Debe confirmar su contraseña de administrador.';
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => $msg]); exit; }
    $_SESSION['errores'] = $msg;
    header("Location: ../../../vistas/admin/rgpd/index.php"); exit;
}

$resultado = eliminarEstudianteRGPD($idEstudiante, $motivo, (int)$_SESSION['idAdmin'], $adminPassword);

if ($resultado['ok']) {
    registrarAccion('rgpd_borrar', 'estudiantes', $idEstudiante, "RGPD Art.17: $motivo");
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit;
}

$_SESSION[$resultado['ok'] ? 'exito' : 'errores'] = $resultado['msg'];
header("Location: ../../../vistas/admin/rgpd/index.php");
exit;

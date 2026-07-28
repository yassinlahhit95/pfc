<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_fp_dual');
require_once __DIR__ . "/../../../modelos/fp_dual.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php"); exit;
}

if (isset($_POST['idEmpresa'])) {
    $idEmpresa = (int)$_POST['idEmpresa'];
    if (eliminarEmpresa($idEmpresa)) {
        registrarAccion('eliminar', 'fp_empresas', $idEmpresa, 'Empresa eliminada');
        $ok = true; $msg = "La empresa ha sido eliminada correctamente.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "Ocurrió un error al intentar eliminar la empresa.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/admin/fp_dual/verEmpresas.php");
exit;

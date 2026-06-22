<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$ok  = false;
$msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/ciclos/ciclosArchivados.php"); exit;
}

if (isset($_POST['idCiclo'])) {
    $idCiclo = (int)$_POST['idCiclo'];
    if (restaurarCiclo($idCiclo)) {
        registrarAccion('restaurar', 'ciclos', $idCiclo);
        $ok  = true;
        $msg = 'Ciclo restaurado correctamente.';
        $_SESSION['exito'] = $msg;
    } else {
        $msg = 'No se pudo restaurar el ciclo.';
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}

header("Location: ../../../vistas/admin/ciclos/ciclosArchivados.php");
exit;

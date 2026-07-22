<?php
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_pagos')) { http_response_code(403); echo json_encode(['error' => 'Módulo desactivado']); exit; }
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/secretaria/pagos/verPagos.php"); exit;
}

if (isset($_POST['idPago'])) {
    $idPago = (int)($_POST['idPago'] ?? 0);
    $pago = obtenerPagoPorId($idPago);
    if ($pago && eliminarPago($idPago)) {
        // El comprobante deja de usarse: se elimina de ambos almacenamientos
        if (!empty($pago['comprobante'])) {
            $nombreComprobante = basename($pago['comprobante']);
            $ruta = __DIR__ . '/../../../public/uploads/comprobantes/' . $nombreComprobante;
            if (is_file($ruta)) @unlink($ruta);
            require_once __DIR__ . '/../../../include/R2Client.php';
            R2Client::deleteObject('comprobantes/' . $nombreComprobante);
        }
        registrarAccionSecretaria('borrar', 'pagos', $idPago);
        $ok = true; $msg = "Pago eliminado.";
        $_SESSION['exito'] = $msg;
    } else {
        $msg = "No se pudo eliminar el pago.";
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/secretaria/pagos/verPagos.php");
exit;

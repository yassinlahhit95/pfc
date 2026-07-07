<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_gastos')) { http_response_code(403); echo json_encode(['error' => 'Módulo desactivado']); exit; }
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/secretarias.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$ok  = false;
$msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/secretaria/gastos/verGastos.php"); exit;
}

if (isset($_POST['idGasto'])) {
    $idGasto = (int)$_POST['idGasto'];
        require_once __DIR__ . '/../../../modelos/log.php';
        registrarAccionSecretaria('borrar', 'gastos', $idGasto, "");
        $ok  = true;
        $msg = 'Gasto eliminado correctamente.';
        $_SESSION['exito'] = $msg;
    } else {
        $msg = 'No se pudo eliminar el gasto.';
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}

header("Location: ../../../vistas/secretaria/gastos/verGastos.php");
exit;

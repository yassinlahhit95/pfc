<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$ok  = false;
$msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/secretaria/gastos/categorias.php"); exit;
}

if (isset($_POST['idCategoria'])) {
    $id = (int)$_POST['idCategoria'];
    // Block deletion if category has gastos
    $count = contarGastosPorCategoria($id);
    if ($count > 0) {
        $msg = "No se puede eliminar: tiene {$count} gasto(s) asociado(s). Reasígnalos o elimínalos primero.";
    } elseif (borrarCategoria($id)) {
        registrarAccionSecretaria('borrar', 'categorias_gasto', $id);
        $ok  = true;
        $msg = 'Categoría eliminada correctamente.';
        $_SESSION['exito'] = $msg;
    } else {
        $msg = 'Error al eliminar la categoría.';
        $_SESSION['errores'] = $msg;
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}

header("Location: ../../../vistas/secretaria/gastos/categorias.php");
exit;

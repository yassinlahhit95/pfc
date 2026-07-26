<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$ok  = false;
$msg = 'Datos incompletos';

if (isset($_POST['actualizarCategoria'])) {
    if (!Security::validateCSRFToken(null, false)) {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
        header("Location: ../../../vistas/secretaria/gastos/categorias.php"); exit;
    }
    $idCategoria      = (int)($_POST['idCategoria'] ?? 0);
    $nombre           = trim($_POST['nombre'] ?? '');
    $presupuestoAnual = filter_var($_POST['presupuestoAnual'] ?? 0, FILTER_VALIDATE_FLOAT);
    $color            = $_POST['color'] ?? '#4F46E5';

    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) { $color = '#4F46E5'; }

    if (!$idCategoria) {
        $msg = 'ID no válido.';
    } elseif (empty($nombre)) {
        $msg = 'El nombre es obligatorio.';
    } elseif ($presupuestoAnual === false || $presupuestoAnual < 0) {
        $msg = 'El presupuesto debe ser un número positivo.';
    } else {
        if (actualizarCategoria($idCategoria, $nombre, (float)$presupuestoAnual, $color)) {
            registrarAccionSecretaria('actualizar', 'categorias_gasto', $idCategoria, $nombre);
            $ok  = true;
            $msg = 'Categoría actualizada correctamente.';
            $_SESSION['exito'] = $msg;
        } else {
            $msg = 'Error al actualizar la categoría.';
            $_SESSION['errores'] = $msg;
        }
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

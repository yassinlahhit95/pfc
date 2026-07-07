<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$ok  = false;
$msg = 'Datos incompletos';
$data = [];

if (isset($_POST['insertarCategoria'])) {
    if (!Security::validateCSRFToken(null, false)) {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
        header("Location: ../../../vistas/secretaria/gastos/categorias.php"); exit;
    }
    $nombre          = trim($_POST['nombre'] ?? '');
    $presupuestoAnual = filter_var($_POST['presupuestoAnual'] ?? 0, FILTER_VALIDATE_FLOAT);
    $color            = $_POST['color'] ?? '#4F46E5';

    // Validate hex color
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) { $color = '#4F46E5'; }

    if (empty($nombre)) {
        $msg = 'El nombre de la categoría es obligatorio.';
    } elseif ($presupuestoAnual === false || $presupuestoAnual < 0) {
        $msg = 'El presupuesto debe ser un número positivo.';
    } else {
        $id = insertarCategoria($nombre, (float)$presupuestoAnual, $color);
        if ($id) {
            registrarAccion('insertar', 'categorias_gasto', $id, $nombre);
            $ok   = true;
            $msg  = 'Categoría creada correctamente.';
            $data = ['idCategoria' => $id, 'nombre' => $nombre, 'color' => $color,
                     'presupuestoAnual' => number_format((float)$presupuestoAnual, 2)];
            $_SESSION['exito'] = $msg;
        } else {
            $msg = 'Error al guardar la categoría.';
            $_SESSION['errores'] = $msg;
        }
    }
}

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg, 'data' => $data]);
    exit;
}

header("Location: ../../../vistas/secretaria/gastos/categorias.php");
exit;

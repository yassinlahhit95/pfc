<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/pagos/resolverComprobante.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$_back = "../../../vistas/$rolBase/pagos/" . ($rolBase === 'admin' ? 'verPagosGeneral.php' : 'verPagos.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

$idPago        = (int)($_POST['idPago'] ?? 0);
$decision      = $_POST['decision'] ?? '';
$motivoRechazo = trim($_POST['motivoRechazo'] ?? '');

if (!in_array($decision, ['aprobar', 'rechazar'], true)) {
    header("Location: $_back"); exit;
}
if ($decision === 'rechazar' && $motivoRechazo === '') {
    $_SESSION['errores'] = "Indica el motivo del rechazo.";
    header("Location: $_back"); exit;
}

$pago = obtenerPagoPorId($idPago);
if (!$pago || $pago['estadoComprobante'] !== 'verificando') {
    $_SESSION['errores'] = "Este comprobante ya fue resuelto o no existe.";
    header("Location: $_back"); exit;
}

$aprobar = $decision === 'aprobar';
$ok = resolverComprobantePago($idPago, $aprobar, $aprobar ? null : $motivoRechazo);

if ($ok) {
    $accion = $aprobar ? 'aprobar_comprobante' : 'rechazar_comprobante';
    $rolBase === 'secretaria'
        ? registrarAccionSecretaria($accion, 'pagos', $idPago, $pago['nombreEstudiante'] ?? '')
        : registrarAccion($accion, 'pagos', $idPago, $pago['nombreEstudiante'] ?? '');
}

$_SESSION[$ok ? 'exito' : 'errores'] = $ok
    ? ($aprobar ? "Comprobante aprobado." : "Comprobante rechazado.")
    : "No se pudo procesar el comprobante. Inténtalo de nuevo.";
header("Location: $_back"); exit;

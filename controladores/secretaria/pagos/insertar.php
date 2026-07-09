<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_pagos');
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/pagos/agregarPago.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/pagos/agregarPago.php"); exit;
}

$idEstudiante   = (int)($_POST['idEstudiante'] ?? 0);
$monto          = (float)($_POST['monto'] ?? 0);
$tipoPago       = Security::sanitize($_POST['tipoPago'] ?? '');
$fechaPago      = Security::sanitize($_POST['fechaPago'] ?? '');

$tiposValidos = ['mensual', 'trimestral', 'semestral', 'unico'];

$errores = [];
if ($idEstudiante <= 0) $errores[] = "Debes seleccionar un estudiante.";
if ($monto <= 0)        $errores[] = "El importe debe ser mayor que 0.";
if (!in_array($tipoPago, $tiposValidos, true)) $errores[] = "El tipo de pago no es válido.";
if (empty($fechaPago))  $errores[] = "La fecha de pago es obligatoria.";

// El importe no puede superar lo pendiente del estudiante (misma regla que el panel de dirección)
if (empty($errores)) {
    $estadoFinanciero = obtenerEstadoFinancieroEstudiante($idEstudiante);
    if ($monto > ($estadoFinanciero['restante'] + 0.05)) {
        $errores[] = "La cantidad no puede superar el importe pendiente del estudiante.";
    }
}

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/pagos/agregarPago.php?idEstudiante=$idEstudiante");
    exit;
}

// Próximo vencimiento derivado del tipo de pago (nunca del cliente)
if ($tipoPago === 'mensual') {
    $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' + 1 month'));
} elseif ($tipoPago === 'trimestral') {
    $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' + 3 months'));
} elseif ($tipoPago === 'semestral') {
    $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' + 6 months'));
} else {
    $fechaProximo = null;
}

// --- SUBIDA DE COMPROBANTE ---
$nombreComprobante = null;
if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
    $directorioUpload = __DIR__ . '/../../../public/uploads/comprobantes/';
    if (!is_dir($directorioUpload)) {
        mkdir($directorioUpload, 0755, true);
    }
    $extension = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'];
    if (in_array($extension, $extensionesPermitidas)) {
        $nombreComprobante = 'pago_sec_' . $idEstudiante . '_' . time() . '.' . $extension;
        $rutaDestino = $directorioUpload . $nombreComprobante;
        if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaDestino)) {
            $nombreComprobante = null;
        }
    }
}
// ------------------------------

$ok = insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo ?: null, $nombreComprobante);

if ($ok) {
    registrarAccionSecretaria('insertar', 'pagos', null, "$tipoPago — {$monto}€");
    $_SESSION['exito'] = "Pago registrado correctamente.";
} else {
    $_SESSION['errores'] = "Error al registrar el pago.";
}
header("Location: ../../../vistas/secretaria/pagos/verPagos.php");
exit;

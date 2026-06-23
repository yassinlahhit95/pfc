<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarPago'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
        exit;
    }
    $idPago       = (int)($_POST['idPago'] ?? 0);
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $tipoPago     = trim($_POST['tipoPago']);
    $monto        = trim($_POST['cantidadPago']);
    $fechaPago    = trim($_POST['fechaPago']);
    $proximaFecha = trim($_POST['fechaProximoPago']);

    $errores = [];
    if (empty($idEstudiante))          $errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    if (empty($monto) || $monto <= 0)  $errores['cantidadPago'] = "La cantidad debe ser un número positivo.";
    if (empty($fechaPago))             $errores['fechaPago'] = "La fecha del pago es un campo obligatorio.";

    if (empty($errores)) {
        if (actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha)) {
            registrarAccion('actualizar', 'pagos', $idPago, "Estudiante #$idEstudiante · $monto€");
            $_SESSION['exito'] = "El pago ha sido actualizado correctamente.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el pago.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_pago'] = $_POST;
    }

    header("Location: ../../../vistas/admin/pagos/modificarPagos.php?idPago=$idPago");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;

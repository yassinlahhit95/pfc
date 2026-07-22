<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/ImageOptimizer.php";
require_once __DIR__ . "/../../../include/R2Client.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarPago'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/pagos/agregarPagos.php");
        exit;
    }
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $tipoPago     = $_POST['tipoPago'];
    $monto        = trim($_POST['monto']);
    $fechaPago    = trim($_POST['fechaPago']);

    $errores = [];
    if (empty($tipoPago)) $errores['tipoPago'] = "Debe elegir un tipo de pago.";

    if (empty($monto)) {
        $errores['monto'] = "La cantidad a cobrar es un campo obligatorio.";
    } elseif (!is_numeric($monto) || $monto <= 0) {
        $errores['monto'] = "La cantidad debe ser un número positivo.";
    }

    if (empty($errores)) {
        $estadoFinanciero = obtenerEstadoFinancieroEstudiante($idEstudiante);
        if ($monto > ($estadoFinanciero['restante'] + 0.05)) {
            $errores['monto'] = "La cantidad no puede superar el importe pendiente del estudiante.";
        }
    }

    if (empty($errores)) {
        if ($tipoPago == 'mensual') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 1 month'));
        } elseif ($tipoPago == 'trimestral') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 3 months'));
        } elseif ($tipoPago == 'semestral') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 6 months'));
        } else {
            $proximaFecha = null;
        }

        // --- SUBIDA DE COMPROBANTE ---
        $nombreComprobante = null;
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
            $mimesPermitidos = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeReal = $finfo ? finfo_file($finfo, $_FILES['comprobante']['tmp_name']) : '';
            if ($finfo) finfo_close($finfo);
            if (isset($mimesPermitidos[$extension]) && $mimeReal === $mimesPermitidos[$extension]) {
                // Nombre aleatorio: evita colisiones (dos pagos del mismo estudiante en el
                // mismo segundo pisarían el comprobante anterior con solo time()) y evita
                // exponer el id del estudiante en el nombre de fichero.
                $nombreComprobante = 'pago_admin_' . bin2hex(random_bytes(16)) . '.' . $extension;
                $tmpName = $_FILES['comprobante']['tmp_name'];

                if ($extension !== 'pdf') ImageOptimizer::optimize($tmpName, $mimeReal); // optimizar el temporal ANTES de subir a R2

                $bytes   = file_get_contents($tmpName);
                $subioOk = $bytes !== false && R2Client::putObject('comprobantes/' . $nombreComprobante, $bytes, $mimeReal);
                @unlink($tmpName);
                if (!$subioOk) $nombreComprobante = null;
            }
        }
        // ------------------------------

        if (insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha, $nombreComprobante)) {
            registrarAccion('insertar', 'pagos', null, "Estudiante #$idEstudiante · $monto€ · $tipoPago");
            $_SESSION['exito'] = "El pago ha sido registrado correctamente.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al registrar el pago en la base de datos.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_pago'] = $_POST;
    }

    header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;

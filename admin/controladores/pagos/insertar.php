<?php
session_start();
require_once "../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_pagos']);

    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $concepto = trim($_POST['concepto'] ?? '');
    $monto = trim($_POST['monto'] ?? '');
    $tipoPago = trim($_POST['tipoPago'] ?? '');
    $estadoPago = trim($_POST['estadoPago'] ?? '');
    $fechaPago = trim($_POST['fechaPago'] ?? '');
    $errores = [];

    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "El estudiante es obligatorio";
    }

    if (empty($concepto)) {
        $errores['concepto'] = "El concepto es obligatorio";
    }

    if (empty($monto)) {
        $errores['monto'] = "El monto es obligatorio";
    } elseif (!is_numeric($monto)) {
        $errores['monto'] = "El monto debe ser un valor numérico";
    } elseif (!preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $monto)) {
        $errores['monto'] = "El monto debe ser un número positivo con hasta dos decimales (ej: 100.00)";
    } elseif (floatval($monto) < 0) {
        $errores['monto'] = "El monto no puede ser negativo";
    }

    if (empty($tipoPago)) {
        $errores['tipoPago'] = "El tipo de pago es obligatorio";
    }

    if (empty($estadoPago)) {
        $errores['estadoPago'] = "El estado del pago es obligatorio";
    }

    if (empty($fechaPago)) {
        $errores['fechaPago'] = "La fecha de pago es obligatoria";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_pagos'] = $_POST;
        header("Location: ../../vistas/pagos/agregarPagos.php");
        exit;
    }

    // Manejo de archivo
    $rutaComprobante = null;
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] == 0) {
        $directorioDestino = "../../uploads/comprobantes/";
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }
        $nombreArchivo = time() . "_" . basename($_FILES['comprobante']['name']);
        $rutaCompleta = $directorioDestino . $nombreArchivo;
        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], $rutaCompleta)) {
            $rutaComprobante = "uploads/comprobantes/" . $nombreArchivo;
        }
    }

    $modelo = new pago();
    if ($modelo->insertarPagoModelo($idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $rutaComprobante)) {
        $_SESSION['exito'] = "Pago registrado correctamente";
    } else {
        $_SESSION['error'] = "Error al registrar el pago";
    }

    header("Location: ../../vistas/pagos/verPagosGeneral.php");
    exit;
}
?>

<?php
session_start();
require_once "../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $concepto = trim($_POST['concepto'] ?? '');
    $monto = $_POST['monto'] ?? 0;
    $tipoPago = $_POST['tipoPago'] ?? '';
    $estadoPago = $_POST['estadoPago'] ?? '';
    $fechaPago = $_POST['fechaPago'] ?? date('Y-m-d');

    // Guardamos para persistencia
    $_SESSION['datos_pagos'] = $_POST;

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
        header("Location: ../../vistas/pagos/agregarPagos.php");
        exit;
    }

    if (empty($concepto)) {
        $_SESSION['error'] = "El concepto es obligatorio.";
        header("Location: ../../vistas/pagos/agregarPagos.php");
        exit;
    }

    // --- SISTEMA DE SUBIDA DE ARCHIVOS (SIMPLE) ---
    $nombreArchivo = null;
    if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = "../../uploads/";
        if (!is_dir($directorioSubida)) {
            mkdir($directorioSubida, 0777, true);
        }

        $nombreOriginal = $_FILES['comprobante']['name'];
        $nombreArchivo = time() . "_" . $nombreOriginal;
        move_uploaded_file($_FILES['comprobante']['tmp_name'], $directorioSubida . $nombreArchivo);
    }

    if (insertarPago($idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $nombreArchivo)) {
        unset($_SESSION['datos_pagos']);
        $_SESSION['exito'] = "Pago registrado con éxito.";
        header("Location: ../../vistas/pagos/verPagosGeneral.php");
    } else {
        $_SESSION['error'] = "Error al guardar el pago en la base de datos.";
        header("Location: ../../vistas/pagos/agregarPagos.php");
    }
    exit;
}

header("Location: ../../vistas/pagos/verPagosGeneral.php");
exit;
?>

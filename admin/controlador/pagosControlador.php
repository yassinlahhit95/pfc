<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/pagos.php";

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$modeloPago = new pago($conexionBD);

if (isset($_POST['guardarPago'])) {
    $accion = $_POST['accion'];
    $idPago = $_POST['idPago'] ?? null;
    
    unset($_SESSION['errores'], $_SESSION['datos_pagos']);

    $errores = [];
    
    $idEstudiante = $_POST['idEstudiante'] ?? '';
    $concepto = trim($_POST['concepto'] ?? '');
    $monto = trim($_POST['monto'] ?? '');
    $tipoPago = $_POST['tipoPago'] ?? '';
    $estadoPago = $_POST['estadoPago'] ?? '';
    $fechaPago = $_POST['fechaPago'] ?? '';

    // --- VALIDACIONES ---
    if (empty($idEstudiante)) { $errores['idEstudiante'] = "Debe seleccionar un alumno."; }
    if (empty($concepto)) { $errores['concepto'] = "El concepto es obligatorio."; }
    if (empty($monto)) { $errores['monto'] = "El monto es obligatorio."; }
    if (empty($tipoPago)) { $errores['tipoPago'] = "El tipo de pago es obligatorio."; }
    if (empty($estadoPago)) { $errores['estadoPago'] = "El estado de pago es obligatorio."; }
    if (empty($fechaPago)) { $errores['fechaPago'] = "La fecha es obligatoria."; }

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_pagos'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarPagos.php" : "modificarPagos.php?id=" . $idPago;
        header("Location: ../vistas/pagos/" . $url);
        exit;
    }

    $datos = [
        'idEstudiante' => $idEstudiante,
        'concepto' => $concepto,
        'monto' => $monto,
        'tipoPago' => $tipoPago,
        'estadoPago' => $estadoPago,
        'fechaPago' => $fechaPago
    ];

    if ($accion == 'insertar') {
        if ($modeloPago->insertarPagoModelo($datos)) {
            $_SESSION['exito'] = "Pago registrado correctamente.";
        }
    } else if ($accion == 'actualizar') {
        $datos['idPago'] = $idPago;
        if ($modeloPago->actualizarPagoModelo($datos)) {
            $_SESSION['exito'] = "Pago actualizado correctamente.";
        }
    }
    
    header("Location: ../vistas/pagos/verPagosGeneral.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['idPago'];
    if ($modeloPago->eliminarPagoModelo($id)) {
        $_SESSION['exito'] = "Registro de pago eliminado.";
    }
    header("Location: ../vistas/pagos/verPagosGeneral.php");
    exit;
}
?>
<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['actualizarPago'])) {
    $idPago = trim($_POST['idPago']);
    $idEstudiante = trim($_POST['idEstudiante']);
    $tipoPago = trim($_POST['tipoPago']);
    $monto = trim($_POST['cantidadPago']); 
    $fechaPago = trim($_POST['fechaPago']);
    $proximaFecha = trim($_POST['fechaProximoPago']);

    $hayError = false;

    if (empty($idEstudiante) || empty($monto) || $monto <= 0 || empty($fechaPago)) {
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha);
        if ($resultado) {
            $_SESSION['exito'] = "Pago actualizado.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        } else {
            $hayError = true;
        }
    }

    if ($hayError) {
        $_SESSION['error'] = "Error al actualizar.";
    }

    header("Location: ../../../vistas/admin/pagos/modificarPagos.php?idPago=$idPago");
    exit;
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;

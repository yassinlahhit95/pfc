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

    $errores = [];

    if (empty($idEstudiante) || empty($monto) || $monto <= 0 || empty($fechaPago)) {
        $errores['datos'] = "Error al actualizar.";
    }

    if (empty($errores)) {
        $resultado = actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha);
        if ($resultado) {
            $_SESSION['exito'] = "Pago actualizado.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        }
        $_SESSION['error'] = "Error al actualizar.";
    } else {
        $_SESSION['error'] = $errores['datos'];
    }

    header("Location: ../../../vistas/admin/pagos/modificarPagos.php?idPago=$idPago");
    exit;
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>

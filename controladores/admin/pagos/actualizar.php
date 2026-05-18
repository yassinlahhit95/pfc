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

    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($monto) || $monto <= 0) {
        $errores['cantidadPago'] = "La cantidad debe ser un número positivo.";
    }
    if (empty($fechaPago)) {
        $errores['fechaPago'] = "La fecha de pago es obligatoria.";
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
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_pago'] = $_POST;
    }

    header("Location: ../../../vistas/admin/pagos/modificarPagos.php?idPago=$idPago");
    exit;
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>

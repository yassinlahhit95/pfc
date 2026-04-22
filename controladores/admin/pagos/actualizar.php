<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['actualizarPago'])) {
    $idPago = $_POST['idPago'];
    $idEstudiante = $_POST['idEstudiante'];
    $monto = $_POST['monto'];
    $tipoPago = $_POST['tipoPago'];
    $fechaPago = $_POST['fechaPago'];

    if ($tipoPago == 'mensual') {
        $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' +1 month'));
    } else if ($tipoPago == 'trimestral') {
        $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' +3 months'));
    } else if ($tipoPago == 'semestral') {
        $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' +6 months'));
    } else {
        $fechaProximo = $fechaPago;
    }

    if (empty($idPago)) {
        header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
        exit;
    } else if (empty($idEstudiante)) {
        $_SESSION['error'] = "Estudiante vacio";
    } else if (!is_numeric($monto)) {
        $_SESSION['error'] = "Monto debe ser numero";
    } else if (actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, "")) {
        $_SESSION['exito'] = "Pago actualizado";
        header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$idPago");
    exit;
}
header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;
?>
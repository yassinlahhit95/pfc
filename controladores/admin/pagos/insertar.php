<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $monto = $_POST['monto'];
    $tipoPago = $_POST['tipoPago'];
    $fechaPago = date('Y-m-d');

    if ($tipoPago == 'mensual') {
        $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' +1 month'));
    } else if ($tipoPago == 'trimestral') {
        $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' +3 months'));
    } else if ($tipoPago == 'semestral') {
        $fechaProximo = date('Y-m-d', strtotime($fechaPago . ' +6 months'));
    } else {
        $fechaProximo = $fechaPago;
    }

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Estudiante vacio";
    } else if (!is_numeric($monto)) {
        $_SESSION['error'] = "Monto debe ser numero";
    } else if (insertarPago($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, "")) {
        $_SESSION['exito'] = "Pago guardado";
        header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/admin/pagos/agregarPagos.php");
    exit;
}
header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;
?>
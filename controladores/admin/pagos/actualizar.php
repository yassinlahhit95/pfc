<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['actualizarPago'])) {
    $idPago = $_POST['idPago'];
    $idEstudiante = $_POST['idEstudiante'];
    $tipoPago = $_POST['tipoPago'];
    $monto = $_POST['cantidadPago']; // Nota: en el form puse cantidadPago
    $fechaPago = $_POST['fechaPago'];
    $fechaProximo = $_POST['fechaProximoPago'];

    $lista_de_errores = [];

    if (empty($idEstudiante)) {
        $lista_de_errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($monto) || $monto <= 0) {
        $lista_de_errores['cantidadPago'] = "La cantidad debe ser mayor a 0.";
    }
    if (empty($fechaPago)) {
        $lista_de_errores['fechaPago'] = "La fecha es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo);
        if ($resultado) {
            $_SESSION['exito'] = "Pago actualizado correctamente.";
            header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_pago'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$idPago");
    exit;
}

header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;

<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['actualizarPago'])) {
    $id_pago = $_POST['idPago'];
    $id_estudiante = $_POST['idEstudiante'];
    $concepto = trim($_POST['conceptoPago']);
    $cantidad = $_POST['cantidadPago'];
    $fecha = $_POST['fechaPago'];

    $lista_de_errores = [];

    if (empty($id_estudiante)) {
        $lista_de_errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($concepto)) {
        $lista_de_errores['conceptoPago'] = "El concepto es obligatorio.";
    }
    if (empty($cantidad)) {
        $lista_de_errores['cantidadPago'] = "La cantidad es obligatoria.";
    } else {
        if (!is_numeric($cantidad)) {
            $lista_de_errores['cantidadPago'] = "La cantidad debe ser un número.";
        }
    }
    if (empty($fecha)) {
        $lista_de_errores['fechaPago'] = "La fecha es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarPago($id_pago, $id_estudiante, $concepto, $cantidad, $fecha);
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

    header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$id_pago");
    exit;
}

header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;

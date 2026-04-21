<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['actualizarPago'])) {
    $id = $_POST['idPago'];
    $idEstudiante = $_POST['idEstudiante'];
    $concepto = trim($_POST['concepto']);
    $monto = $_POST['monto'];
    $tipo = $_POST['tipoPago'];
    $estado = $_POST['estadoPago'];
    $fecha = $_POST['fechaPago'];

    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($id)) {
        header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
    } else if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
        header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$id");
    } else if (empty($concepto)) {
        $_SESSION['error'] = "El concepto es obligatorio.";
        header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$id");
    } else if (!empty($monto) && !is_numeric($monto)) {
        $_SESSION['error'] = "El monto debe ser un valor numérico.";
        header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$id");
    } else if (!empty($fecha) && !preg_match($regexFecha, $fecha)) {
        $_SESSION['error'] = "La fecha no es válida.";
        header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$id");
    } else {
        // --- SISTEMA DE SUBIDA DE ARCHIVOS (SIMPLE) ---
        $nombreArchivo = null;
        if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
            $directorioSubida = "../../public/uploads/";
            if (!is_dir($directorioSubida)) {
                mkdir($directorioSubida, 0777, true);
            }

            $nombreOriginal = $_FILES['comprobante']['name'];
            $nombreArchivo = time() . "_" . $nombreOriginal;
            move_uploaded_file($_FILES['comprobante']['tmp_name'], $directorioSubida . $nombreArchivo);
        }

        if (actualizarPago($id, $idEstudiante, $concepto, $monto, $tipo, $estado, $fecha, $nombreArchivo)) {
            $_SESSION['exito'] = "Pago actualizado correctamente.";
            header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
        } else {
            $_SESSION['error'] = "Error al actualizar el pago en la base de datos.";
            header("Location: /pfc/vistas/admin/pagos/modificarPagos.php?idPago=$id");
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;
?>
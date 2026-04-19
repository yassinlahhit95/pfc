<?php
session_start();
require_once "../../modelos/pagos.php";

if (isset($_POST['actualizarPago'])) {
    $id = $_POST['idPago'];
    $idEstudiante = $_POST['idEstudiante'];
    $concepto = trim($_POST['concepto'] ?? '');
    $monto = $_POST['monto'] ?? 0;
    $tipo = $_POST['tipoPago'] ?? '';
    $estado = $_POST['estadoPago'] ?? '';
    $fecha = $_POST['fechaPago'] ?? date('Y-m-d');

    if (empty($id)) {
        header("Location: ../../vistas/pagos/verPagosGeneral.php");
        exit;
    }

    if (empty($idEstudiante) || empty($concepto)) {
        $_SESSION['error'] = "El alumno y el concepto son obligatorios.";
        header("Location: ../../vistas/pagos/modificarPagos.php?idPago=$id");
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

    if (actualizarPago($id, $idEstudiante, $concepto, $monto, $tipo, $estado, $fecha, $nombreArchivo)) {
        $_SESSION['exito'] = "Pago actualizado correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar el pago en la base de datos.";
    }
}

header("Location: ../../vistas/pagos/verPagosGeneral.php");
exit;
?>

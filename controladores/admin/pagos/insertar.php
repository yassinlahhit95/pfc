<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $concepto = trim($_POST['concepto']);
    $monto = $_POST['monto'];
    $tipoPago = $_POST['tipoPago'];
    $estadoPago = $_POST['estadoPago'];
    $fechaPago = $_POST['fechaPago'];

    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Debe seleccionar un estudiante.";
        header("Location: /pfc/vistas/admin/pagos/agregarPagos.php");
    } else if (empty($concepto)) {
        $_SESSION['error'] = "El concepto es obligatorio.";
        header("Location: /pfc/vistas/admin/pagos/agregarPagos.php");
    } else if (!empty($monto) && !is_numeric($monto)) {
        $_SESSION['error'] = "El monto debe ser un valor numérico.";
        header("Location: /pfc/vistas/admin/pagos/agregarPagos.php");
    } else if (!empty($fechaPago) && !preg_match($regexFecha, $fechaPago)) {
        $_SESSION['error'] = "La fecha no es válida.";
        header("Location: /pfc/vistas/admin/pagos/agregarPagos.php");
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

        if (insertarPago($idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $nombreArchivo)) {
            $_SESSION['exito'] = "Pago registrado con éxito.";
            header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
        } else {
            $_SESSION['error'] = "Error al guardar el pago en la base de datos.";
            header("Location: /pfc/vistas/admin/pagos/agregarPagos.php");
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;
?>
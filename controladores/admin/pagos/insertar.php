<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $tipoPago     = $_POST['tipoPago'];
    $monto        = trim($_POST['monto']);
    $fechaPago    = trim($_POST['fechaPago']);

    $hoy         = date('Y-m-d');
    $fechaLimite = date('Y') . '-06-30';

    $errores = '';

    if (empty($tipoPago)) {
        $errores = "Debes elegir un tipo de pago.";
    }

    if (empty($monto)) {
        $errores = "La cantidad a cobrar es obligatoria.";
    } else if (!is_numeric($monto) || $monto <= 0) {
        $errores = "La cantidad debe ser un número positivo.";
    }

    if ($hoy > $fechaLimite) {
        $_SESSION['errores'] = "Periodo de pagos terminado.";
        header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
        exit;
    }

    if (!$errores) {
        $estadoFinanciero = obtenerEstadoFinancieroEstudiante($idEstudiante);
        if ($monto > ($estadoFinanciero['restante'] + 0.05)) {
            $errores = "La cantidad no puede superar el pendiente.";
        }
    }

    if (!$errores) {
        if ($tipoPago == 'mensual') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 1 month'));
        } elseif ($tipoPago == 'trimestral') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 3 months'));
        } elseif ($tipoPago == 'semestral') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 6 months'));
        } else {
            $proximaFecha = $fechaLimite;
        }

        if ($proximaFecha > $fechaLimite) {
            $proximaFecha = $fechaLimite;
        }

        $resultado = insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha);

        if ($resultado) {
            $_SESSION['exito'] = "Pago registrado correctamente.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        }
        $_SESSION['errores'] = "Error al registrar el pago en la base de datos.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_pago'] = $_POST;
    }

    header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>

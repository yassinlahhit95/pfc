<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $tipoPago     = trim($_POST['tipoPago']);
    $monto        = trim($_POST['monto']);
    $fechaPago    = trim($_POST['fechaPago']);

    $hoy         = date('Y-m-d');
    $fechaLimite = date('Y') . '-06-30';

    $errores = [];

    if (empty($idEstudiante) || empty($tipoPago) || empty($monto) || $monto <= 0) {
        $errores['datos'] = "Error en datos.";
    }

    if ($hoy > $fechaLimite) {
        $_SESSION['error'] = "Periodo de pagos terminado.";
        header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
        exit;
    }

    if (empty($errores)) {
        $estadoFinanciero = obtenerEstadoFinancieroEstudiante($idEstudiante);
        if ($monto > ($estadoFinanciero['restante'] + 0.05)) {
            $errores['monto'] = "Cantidad excedida.";
            $_SESSION['error'] = "Cantidad excedida.";
        }
    }

    if (empty($errores)) {
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

        $ok = insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha);

        if ($ok) {
            $_SESSION['exito'] = "Pago registrado.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        }
        $_SESSION['error'] = "Error al registrar el pago.";
    } elseif (empty($_SESSION['error'])) {
        $_SESSION['error'] = $errores['datos'];
    }

    header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>

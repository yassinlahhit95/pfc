<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $tipoPago = trim($_POST['tipoPago']);
    $monto = trim($_POST['monto']);
    $fechaPago = trim($_POST['fechaPago']);

    $hoy = date('Y-m-d');
    $fechaLimite = date('Y') . '-06-30';

    $hayError = false;

    // Validaciones bÃ¡sicas
    if (empty($idEstudiante) || empty($tipoPago) || empty($monto) || $monto <= 0) {
        $hayError = true;
    }

    // Fecha tope para pagos
    if ($hoy > $fechaLimite) {
        $_SESSION['error'] = "Periodo terminado.";
        header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
        exit;
    }

    if (!$hayError) {
        // Comprobar que no se pague mÃ¡s de lo debido
        $estadoFinanciero = obtenerEstadoFinancieroEstudiante($idEstudiante);
        if ($monto > ($estadoFinanciero['restante'] + 0.05)) {
            $hayError = true;
            $_SESSION['error'] = "Cantidad excedida.";
        }
    }

    if (!$hayError) {
        // Calcular la fecha del prÃ³ximo pago de forma sencilla
        $proximaFecha = "";
        if ($tipoPago == 'mensual') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 1 month'));
        } else if ($tipoPago == 'trimestral') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 3 months'));
        } else if ($tipoPago == 'semestral') {
            $proximaFecha = date('Y-m-d', strtotime($fechaPago . ' + 6 months'));
        } else {
            // Si es pago Ãºnico, la prÃ³xima fecha es el fin del curso
            $proximaFecha = $fechaLimite;
        }

        // Ajustar si se pasa de Junio
        if ($proximaFecha > $fechaLimite) {
            $proximaFecha = $fechaLimite;
        }

        $resultado = insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $proximaFecha);

        if ($resultado) {
            $_SESSION['exito'] = "Pago registrado.";
            header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
            exit;
        } else {
            $hayError = true;
        }
    }

    if ($hayError && empty($_SESSION['error'])) {
        $_SESSION['error'] = "Error en datos.";
    }

    header("Location: ../../../vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/pagos/verPagosGeneral.php");
exit;
?>>
<?php
session_start();
require_once "../../../modelos/pagos.php";

if (isset($_POST['guardarPago'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $tipoPago = $_POST['tipoPago'];
    $monto = $_POST['monto'];
    $fechaPago = $_POST['fechaPago'];

    $hoy = date('Y-m-d');
    $fechaLimite = date('Y') . '-06-30';

    if ($hoy > $fechaLimite) {
        $_SESSION['error'] = "Error: El periodo de pagos para este año ha finalizado (30 de Junio).";
        header("Location: /pfc/vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
        exit;
    }

    $lista_de_errores = [];

    if (empty($idEstudiante)) {
        $lista_de_errores['idEstudiante'] = "Debe seleccionar un estudiante.";
    }
    if (empty($tipoPago)) {
        $lista_de_errores['tipoPago'] = "El tipo de pago es obligatorio.";
    }
    if (empty($monto) || $monto <= 0) {
        $lista_de_errores['monto'] = "La cantidad debe ser mayor a 0.";
    }

    if (empty($lista_de_errores)) {
        // Verificar balance en servidor (Seguridad extra)
        $estado = obtenerEstadoFinancieroEstudiante($idEstudiante);
        if ($monto > ($estado['restante'] + 0.05)) { // Tolerancia pequeña por redondeos
            $_SESSION['error'] = "Error: El pago supera la cantidad pendiente del estudiante.";
            header("Location: /pfc/vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
            exit;
        }

        // Calcular fechaProximoPago basado en tipoPago
        $fechaObj = new DateTime($fechaPago);
        if ($tipoPago == 'mensual') {
            $fechaObj->modify('+1 month');
        } else if ($tipoPago == 'trimestral') {
            $fechaObj->modify('+3 months');
        } else if ($tipoPago == 'semestral') {
            $fechaObj->modify('+6 months');
        } else {
            // Unico - Marcamos como fin de ciclo
            $fechaObj = new DateTime($fechaLimite);
        }
        
        $fechaProximo = $fechaObj->format('Y-m-d');
        
        // Capped at June 30th
        if ($fechaProximo > $fechaLimite) {
            $fechaProximo = $fechaLimite;
        }
        
        $resultado = insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo);
        
        if ($resultado) {
            $_SESSION['exito'] = "Pago registrado correctamente.";
            header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_pago'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/pagos/agregarPagos.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
exit;


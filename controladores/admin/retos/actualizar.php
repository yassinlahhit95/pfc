<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizarReto'])) {
    $idRetoActualizar = trim($_POST['idReto']);
    $nombreRetoActualizar = trim($_POST['nombreReto']);
    $horasDelReto = trim($_POST['horasReto']);
    $fechaInicioDelReto = trim($_POST['fechaInicioReto']);
    $fechaFinDelReto = trim($_POST['fechaFinReto']);
    $listaModulosAsociados = isset($_POST['modulosReto']) ? $_POST['modulosReto'] : [];

    $listaErroresValidacion = [];

    if (empty($nombreRetoActualizar)) {
        $listaErroresValidacion['nombreReto'] = "Vaya, el nombre es obligatorio.";
    }
    if (empty($horasDelReto)) {
        $listaErroresValidacion['horasReto'] = "Vaya, las horas son obligatorias.";
    } else {
        if (!is_numeric($horasDelReto)) {
            $listaErroresValidacion['horasReto'] = "Vaya, las horas deben ser un nÃºmero.";
        }
    }
    if (empty($fechaInicioDelReto)) {
        $listaErroresValidacion['fechaInicioReto'] = "Vaya, la fecha de inicio es obligatoria.";
    }
    if (empty($fechaFinDelReto)) {
        $listaErroresValidacion['fechaFinReto'] = "Vaya, la fecha de fin es obligatoria.";
    }
    if (empty($listaModulosAsociados)) {
        $listaErroresValidacion['modulosReto'] = "Vaya, debes seleccionar al menos un mÃ³dulo.";
    } else if (is_numeric($horasDelReto)) {
        // Validar que el mÃ³dulo tenga suficientes horas disponibles
        foreach ($listaModulosAsociados as $idModuloParaValidar) {
            if (!comprobarHorasDisponiblesModulo($idModuloParaValidar, $horasDelReto, $idRetoActualizar)) {
                $listaErroresValidacion['modulosReto'] = "Vaya, uno de los mÃ³dulos seleccionados no tiene suficientes horas disponibles.";
                break;
            }
        }
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarReto($idRetoActualizar, $nombreRetoActualizar, $fechaInicioDelReto, $fechaFinDelReto, $horasDelReto, $listaModulosAsociados)) {
            $_SESSION['exito'] = "Listo! Reto actualizado correctamente.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, hubo un error al actualizar en la base de datos.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaErroresValidacion;
        
        $datosParaSesion = $_POST;
        $datosParaSesion['fechaInicio'] = $_POST['fechaInicioReto'];
        $datosParaSesion['fechaFin'] = $_POST['fechaFinReto'];
        $_SESSION['datos_reto'] = $datosParaSesion;
    }

    header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idRetoActualizar");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;

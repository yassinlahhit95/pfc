<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardarModulo'])) {
    $idModuloActualizar = trim($_POST['idModulo']);
    $nombreModuloActualizar = trim($_POST['nombreModulo']);
    $idCicloAsociado = trim($_POST['idCiclo']);
    $horasMaximasModulo = trim($_POST['horasMaximas']);

    $listaErroresValidacion = [];

    if (empty($nombreModuloActualizar)) {
        $listaErroresValidacion['nombreModulo'] = "Vaya, el nombre del mÃ³dulo es obligatorio.";
    }
    
    if (empty($idCicloAsociado)) {
        $listaErroresValidacion['idCiclo'] = "Vaya, debes seleccionar un ciclo formativo.";
    }
    
    if (empty($horasMaximasModulo)) {
        $listaErroresValidacion['horasMaximas'] = "Vaya, las horas mÃ¡ximas son obligatorias.";
    } else {
        if (!is_numeric($horasMaximasModulo)) {
            $listaErroresValidacion['horasMaximas'] = "Vaya, las horas deben ser un valor numÃ©rico.";
        }
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarModulo($idModuloActualizar, $nombreModuloActualizar, $idCicloAsociado, $horasMaximasModulo)) {
            $_SESSION['exito'] = "Listo! MÃ³dulo actualizado correctamente.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, error al actualizar el mÃ³dulo en la base de datos.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaErroresValidacion;
        $_SESSION['datos_modulo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModuloActualizar");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;

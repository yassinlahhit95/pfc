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
        $listaErroresValidacion['nombreModulo'] = "Nombre de módulo obligatorio.";
    }
    
    if (empty($idCicloAsociado)) {
        $listaErroresValidacion['idCiclo'] = "Seleccione un ciclo.";
    }
    
    if (empty($horasMaximasModulo)) {
        $listaErroresValidacion['horasMaximas'] = "Horas máximas obligatorias.";
    } else {
        if (!is_numeric($horasMaximasModulo)) {
            $listaErroresValidacion['horasMaximas'] = "Las horas deben ser numéricas.";
        }
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarModulo($idModuloActualizar, $nombreModuloActualizar, $idCicloAsociado, $horasMaximasModulo)) {
            $_SESSION['exito'] = "Módulo actualizado.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Error al actualizar.";
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

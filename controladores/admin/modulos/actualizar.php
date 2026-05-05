<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$hayError = false;

if (isset($_POST['guardarModulo'])) {
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
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlDup = "SELECT idModulo FROM modulos WHERE nombreModulo = '" . mysqli_real_escape_string($con, $nombreModuloActualizar) . "' AND idCiclo = $idCicloAsociado AND idModulo != $idModuloActualizar";
        $resDup = mysqli_query($con, $sqlDup);
        if (mysqli_num_rows($resDup) > 0) {
            $listaErroresValidacion['nombreModulo'] = "Este módulo ya existe en este ciclo.";
        }
        mysqli_close($con);
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarModulo($idModuloActualizar, $nombreModuloActualizar, $idCicloAsociado, $horasMaximasModulo)) {
            $_SESSION['exito'] = "Módulo actualizado.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "No se pudo actualizar el módulo.";
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

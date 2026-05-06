<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $nombreNuevoModulo = trim($_POST['nombreModulo']);
    $idCicloNuevoModulo = trim($_POST['idCiclo']);
    $horasMaximasNuevoModulo = trim($_POST['horasMaximas']);

    $listaErroresValidacion = [];

    if (empty($nombreNuevoModulo)) {
        $listaErroresValidacion['nombreModulo'] = "Nombre de módulo obligatorio.";
    }
    
    if (empty($idCicloNuevoModulo)) {
        $listaErroresValidacion['idCiclo'] = "Seleccione un ciclo.";
    }
    
    if (empty($horasMaximasNuevoModulo)) {
        $listaErroresValidacion['horasMaximas'] = "Horas máximas obligatorias.";
    } else {
        if (!is_numeric($horasMaximasNuevoModulo)) {
            $listaErroresValidacion['horasMaximas'] = "Las horas deben ser numéricas.";
        }
    }

    if (empty($listaErroresValidacion)) {
        if (checkModuloExistente($nombreNuevoModulo, $idCicloNuevoModulo)) {
            $listaErroresValidacion['nombreModulo'] = "Este módulo ya existe en el ciclo seleccionado.";
        }
    }

    if (empty($listaErroresValidacion)) {
        if (insertarModulo($nombreNuevoModulo, $idCicloNuevoModulo, $horasMaximasNuevoModulo)) {
            $_SESSION['exito'] = "Módulo registrado.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el módulo.";
    } else {
        $_SESSION['errores'] = $listaErroresValidacion;
        $_SESSION['datos_modulo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>

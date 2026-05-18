<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $nombreNuevoModulo = trim($_POST['nombreModulo']);
    $idCicloNuevoModulo = $_POST['idCiclo'];
    $horasMaximasNuevoModulo = trim($_POST['horasMaximas']);

    $errores = [];

    if (empty($nombreNuevoModulo)) {
        $errores['nombreModulo'] = "Nombre de módulo obligatorio.";
    }
    
    if (empty($idCicloNuevoModulo)) {
        $errores['idCiclo'] = "Seleccione un ciclo.";
    }
    
    if (empty($horasMaximasNuevoModulo)) {
        $errores['horasMaximas'] = "Horas máximas obligatorias.";
    } else {
        if (!is_numeric($horasMaximasNuevoModulo)) {
            $errores['horasMaximas'] = "Las horas deben ser numéricas.";
        }
    }

    if (empty($errores)) {
        if (checkModuloExistente($nombreNuevoModulo, $idCicloNuevoModulo)) {
            $errores['nombreModulo'] = "Este módulo ya existe en el ciclo seleccionado.";
        }
    }

    if (empty($errores)) {
        if (insertarModulo($nombreNuevoModulo, $idCicloNuevoModulo, $horasMaximasNuevoModulo)) {
            $_SESSION['exito'] = "Módulo registrado.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el módulo.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>

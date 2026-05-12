<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $idModuloActualizar = trim($_POST['idModulo']);
    $nombreModuloActualizar = trim($_POST['nombreModulo']);
    $idCicloAsociado = trim($_POST['idCiclo']);
    $horasMaximasModulo = trim($_POST['horasMaximas']);
    $cursoModulo = intval($_POST['curso'] ?? 1);

    $errores = [];

    if (empty($nombreModuloActualizar)) {
        $errores['nombreModulo'] = "Nombre de módulo obligatorio.";
    }
    
    if (empty($idCicloAsociado)) {
        $errores['idCiclo'] = "Seleccione un ciclo.";
    }
    
    if (empty($horasMaximasModulo)) {
        $errores['horasMaximas'] = "Horas máximas obligatorias.";
    } else {
        if (!is_numeric($horasMaximasModulo)) {
            $errores['horasMaximas'] = "Las horas deben ser numéricas.";
        }
    }

    if (empty($errores)) {
        if (checkModuloExistente($nombreModuloActualizar, $idCicloAsociado, $idModuloActualizar)) {
            $errores['nombreModulo'] = "Este módulo ya existe en este ciclo.";
        }
    }

    if (empty($errores)) {
        if (actualizarModulo($idModuloActualizar, $nombreModuloActualizar, $idCicloAsociado, $horasMaximasModulo, $cursoModulo)) {
            $_SESSION['exito'] = "Módulo actualizado.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el módulo.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModuloActualizar");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>

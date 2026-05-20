<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $idModuloActualizar = trim($_POST['idModulo']);
    $nombreModuloActualizar = trim($_POST['nombreModulo']);
    $idCicloAsociado = trim($_POST['idCiclo']);
    $horasMaximasModulo = trim($_POST['horasMaximas']);

    $errores = [];
    if (empty($nombreModuloActualizar)) $errores['nombreModulo'] = "Nombre del módulo obligatorio.";
    if (empty($idCicloAsociado)) $errores['idCiclo'] = "Seleccione un ciclo formativo.";
    if (empty($horasMaximasModulo)) {
        $errores['horasMaximas'] = "Las horas totales son obligatorias.";
    } elseif (!is_numeric($horasMaximasModulo)) {
        $errores['horasMaximas'] = "Las horas deben ser un valor numérico.";
    }

    if (empty($errores) && checkModuloExistente($nombreModuloActualizar, $idCicloAsociado, $idModuloActualizar)) {
        $errores['nombreModulo'] = "Ya existe otro módulo con este nombre en el ciclo elegido.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModuloActualizar");
        exit;
    }

    if (actualizarModulo($idModuloActualizar, $nombreModuloActualizar, $idCicloAsociado, $horasMaximasModulo)) {
        $_SESSION['exito'] = "Módulo actualizado correctamente.";
        header("Location: ../../../vistas/admin/modulos/verModulos.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo actualizar el módulo.";
    header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModuloActualizar");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>

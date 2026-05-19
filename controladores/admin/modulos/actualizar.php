<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $idModuloActualizar = trim($_POST['idModulo']);
    $nombreModuloActualizar = trim($_POST['nombreModulo']);
    $idCicloAsociado = trim($_POST['idCiclo']);
    $horasMaximasModulo = trim($_POST['horasMaximas']);

    $fallos = [];
    if (empty($nombreModuloActualizar)) $fallos['nombreModulo'] = "Nombre del módulo obligatorio.";
    if (empty($idCicloAsociado)) $fallos['idCiclo'] = "Seleccione un ciclo formativo.";
    if (empty($horasMaximasModulo)) {
        $fallos['horasMaximas'] = "Las horas totales son obligatorias.";
    } elseif (!is_numeric($horasMaximasModulo)) {
        $fallos['horasMaximas'] = "Las horas deben ser un valor numérico.";
    }

    if (empty($fallos) && checkModuloExistente($nombreModuloActualizar, $idCicloAsociado, $idModuloActualizar)) {
        $fallos['nombreModulo'] = "Ya existe otro módulo con este nombre en el ciclo elegido.";
    }

    if (!empty($fallos)) {
        $_SESSION['errores'] = $fallos;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModuloActualizar");
        exit;
    }

    if (actualizarModulo($idModuloActualizar, $nombreModuloActualizar, $idCicloAsociado, $horasMaximasModulo)) {
        $_SESSION['exito'] = "Módulo actualizado correctamente.";
        header("Location: ../../../vistas/admin/modulos/verModulos.php");
        exit;
    }
    $_SESSION['error'] = "No se pudo actualizar el módulo.";
    header("Location: ../../../vistas/admin/modulos/modificarModulos.php?idModulo=$idModuloActualizar");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>

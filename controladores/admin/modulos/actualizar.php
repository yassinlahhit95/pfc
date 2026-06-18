<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $idModuloActualizar = (int)($_POST['idModulo'] ?? 0);
    $nombreModuloActualizar = trim($_POST['nombreModulo']);
    $idCicloAsociado = (int)($_POST['idCiclo'] ?? 0);
    $horasMaximasModulo = trim($_POST['horasMaximas']);

    $errores = '';
    if (empty($nombreModuloActualizar)) $errores = "Nombre del módulo obligatorio.";
    if (empty($idCicloAsociado)) $errores = "Seleccione un ciclo formativo.";
    if (empty($horasMaximasModulo)) {
        $errores = "Las horas totales son obligatorias.";
    } elseif (!is_numeric($horasMaximasModulo)) {
        $errores = "Las horas deben ser un valor numérico.";
    }

    if (!$errores && checkModuloExistente($nombreModuloActualizar, $idCicloAsociado, $idModuloActualizar)) {
        $errores = "Ya existe otro módulo con este nombre en el ciclo elegido.";
    }

    if ($errores) {
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

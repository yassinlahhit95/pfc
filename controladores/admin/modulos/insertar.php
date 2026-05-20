<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

if (isset($_POST['guardarModulo'])) {
    $nombreNuevoModulo = trim($_POST['nombreModulo']);
    $idCicloNuevoModulo = $_POST['idCiclo'];
    $horasMaximasNuevoModulo = trim($_POST['horasMaximas']);

    $avisos = [];
    if (empty($nombreNuevoModulo)) $avisos['nombreModulo'] = "Nombre de módulo obligatorio.";
    if (empty($idCicloNuevoModulo)) $avisos['idCiclo'] = "Seleccione un ciclo.";
    if (empty($horasMaximasNuevoModulo)) {
        $avisos['horasMaximas'] = "Horas máximas obligatorias.";
    } elseif (!is_numeric($horasMaximasNuevoModulo)) {
        $avisos['horasMaximas'] = "Las horas deben ser numéricas.";
    }

    if (empty($avisos) && checkModuloExistente($nombreNuevoModulo, $idCicloNuevoModulo)) {
        $avisos['nombreModulo'] = "Este módulo ya existe en el ciclo seleccionado.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_modulo'] = $_POST;
        header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
        exit;
    }

    if (insertarModulo($nombreNuevoModulo, $idCicloNuevoModulo, $horasMaximasNuevoModulo)) {
        $_SESSION['exito'] = "Módulo registrado.";
        header("Location: ../../../vistas/admin/modulos/verModulos.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo registrar el módulo.";
    header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>

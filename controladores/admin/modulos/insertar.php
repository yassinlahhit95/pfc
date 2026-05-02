<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardarModulo'])) {
    $nombreNuevoModulo = trim($_POST['nombreModulo']);
    $idCicloNuevoModulo = trim($_POST['idCiclo']);
    $horasMaximasNuevoModulo = trim($_POST['horasMaximas']);

    $listaErroresValidacion = [];

    if (empty($nombreNuevoModulo)) {
        $listaErroresValidacion['nombreModulo'] = "Vaya, el nombre del mÃ³dulo es obligatorio.";
    }
    
    if (empty($idCicloNuevoModulo)) {
        $listaErroresValidacion['idCiclo'] = "Vaya, debes seleccionar un ciclo formativo.";
    }
    
    if (empty($horasMaximasNuevoModulo)) {
        $listaErroresValidacion['horasMaximas'] = "Vaya, las horas mÃ¡ximas son obligatorias.";
    } else {
        if (!is_numeric($horasMaximasNuevoModulo)) {
            $listaErroresValidacion['horasMaximas'] = "Vaya, las horas deben ser un valor numÃ©rico.";
        }
    }

    if (empty($listaErroresValidacion)) {
        if (insertarModulo($nombreNuevoModulo, $idCicloNuevoModulo, $horasMaximasNuevoModulo)) {
            $_SESSION['exito'] = "Listo! MÃ³dulo registrado correctamente.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, hubo un error al insertar el mÃ³dulo.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaErroresValidacion;
        $_SESSION['datos_modulo'] = $_POST;
    }

    header("Location: ../../../vistas/admin/modulos/agregarModulos.php");
    exit;
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;

<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$hayError = false;

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
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlDup = "SELECT idModulo FROM modulos WHERE nombreModulo = '" . $nombreNuevoModulo . "' AND idCiclo = $idCicloNuevoModulo";
        $resDup = mysqli_query($con, $sqlDup);
        if (mysqli_num_rows($resDup) > 0) {
            $listaErroresValidacion['nombreModulo'] = "Este módulo ya existe en el ciclo seleccionado.";
        }
        mysqli_close($con);
    }

    if (empty($listaErroresValidacion)) {
        if (insertarModulo($nombreNuevoModulo, $idCicloNuevoModulo, $horasMaximasNuevoModulo)) {
            $_SESSION['exito'] = "Módulo registrado.";
            header("Location: ../../../vistas/admin/modulos/verModulos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "No se pudo registrar el módulo.";
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



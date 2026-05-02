<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizarProfesor'])) {
    $idProfesorActualizar = trim($_POST['idProfesor']);
    $nombreProfesorActualizar = trim($_POST['nombreProfesor']);
    $emailProfesorActualizar = trim($_POST['emailProfesor']);
    $dniProfesorActualizar = trim($_POST['dniProfesor']);
    $telefonoProfesorActualizar = trim($_POST['telefonoProfesor']);
    $direccionProfesorActualizar = trim($_POST['direccionProfesor']);
    
    $fechaNacimientoProfesor = trim($_POST['fechaNacimientoProfesor'] ?? '1980-01-01');
    $fechaAltaProfesor = trim($_POST['fechaAltaProfesor'] ?? '2026-01-01');
    $ciudadProfesor = trim($_POST['ciudadProfesor'] ?? '');
    $codigoPostalProfesor = trim($_POST['codigoPostalProfesor'] ?? '');
    $observacionesProfesor = trim($_POST['observacionesProfesor'] ?? '');

    $listaErroresValidacion = [];

    if (empty($nombreProfesorActualizar)) {
        $listaErroresValidacion['nombreProfesor'] = "Vaya, el nombre es obligatorio.";
    }
    if (empty($emailProfesorActualizar)) {
        $listaErroresValidacion['emailProfesor'] = "Vaya, el email es obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $emailProfesorActualizar)) {
        $listaErroresValidacion['emailProfesor'] = "Vaya, el formato del email no es vÃ¡lido.";
    }
    if (empty($dniProfesorActualizar)) {
        $listaErroresValidacion['dniProfesor'] = "Vaya, el DNI es obligatorio.";
    }
    if (empty($telefonoProfesorActualizar)) {
        $listaErroresValidacion['telefonoProfesor'] = "Vaya, el telÃ©fono es obligatorio.";
    } else if (!is_numeric($telefonoProfesorActualizar)) {
        $listaErroresValidacion['telefonoProfesor'] = "Vaya, el telÃ©fono debe ser numÃ©rico.";
    }
    if (empty($direccionProfesorActualizar)) {
        $listaErroresValidacion['direccionProfesor'] = "Vaya, la direcciÃ³n es obligatoria.";
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarProfesor($idProfesorActualizar, $nombreProfesorActualizar, $emailProfesorActualizar, $telefonoProfesorActualizar, $dniProfesorActualizar, $direccionProfesorActualizar, $fechaNacimientoProfesor, $fechaAltaProfesor, $ciudadProfesor, $codigoPostalProfesor, $observacionesProfesor)) {
            $_SESSION['exito'] = "Listo! Profesor actualizado correctamente.";
            header("Location: ../../../vistas/admin/profesores/verProfesores.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, hubo un error al actualizar en la base de datos.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaErroresValidacion;
        $_SESSION['datos_profesor'] = $_POST;
    }

    header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesorActualizar");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;

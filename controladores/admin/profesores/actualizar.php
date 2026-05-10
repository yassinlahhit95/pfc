<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

if (isset($_POST['actualizarProfesor'])) {
    $idProfesorActualizar = trim($_POST['idProfesor']);
    $nombreProfesorActualizar = trim($_POST['nombreProfesor']);
    $emailProfesorActualizar = trim($_POST['emailProfesor']);
    $dniProfesorActualizar = trim($_POST['dniProfesor']);
    $telefonoProfesorActualizar = trim($_POST['telefonoProfesor']);
    $direccionProfesorActualizar = trim($_POST['direccionProfesor']);
    
    $fechaNacimientoProfesor = trim($_POST['fechaNacimientoProfesor'] ?? '1980-01-01');
    $fechaAltaProfesor = trim($_POST['fechaAltaProfesor'] ?? '2026-01-01');
    $ciudadProfesor = trim($_POST['ciudadProfesor']);
    $codigoPostalProfesor = trim($_POST['codigoPostalProfesor']);
    $observacionesProfesor = trim($_POST['observacionesProfesor']);

    $errores = [];

    if (empty($nombreProfesorActualizar)) {
        $errores['nombreProfesor'] = "El nombre es obligatorio.";
    }
    if (empty($emailProfesorActualizar)) {
        $errores['emailProfesor'] = "El email es obligatorio.";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $emailProfesorActualizar)) {
        $errores['emailProfesor'] = "El formato del email no es válido.";
    }
    if (empty($dniProfesorActualizar)) {
        $errores['dniProfesor'] = "El DNI es obligatorio.";
    }
    if (empty($telefonoProfesorActualizar)) {
        $errores['telefonoProfesor'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefonoProfesorActualizar)) {
        $errores['telefonoProfesor'] = "El teléfono debe ser numérico.";
    }
    if (empty($direccionProfesorActualizar)) {
        $errores['direccionProfesor'] = "La dirección es obligatoria.";
    }
    if (!empty($codigoPostalProfesor) && !is_numeric($codigoPostalProfesor)) {
        $errores['codigoPostalProfesor'] = "El código postal debe ser numérico.";
    }

    if (empty($errores)) {
        if (checkProfesorExistente($dniProfesorActualizar, $emailProfesorActualizar, $idProfesorActualizar)) {
            $errores['dniProfesor'] = "El DNI o Email ya están registrados por otro profesor.";
        }
    }

    if (empty($errores)) {
        if (actualizarProfesor($idProfesorActualizar, $nombreProfesorActualizar, $emailProfesorActualizar, $telefonoProfesorActualizar, $dniProfesorActualizar, $direccionProfesorActualizar, $fechaNacimientoProfesor, $fechaAltaProfesor, $ciudadProfesor, $codigoPostalProfesor, $observacionesProfesor)) {
            $_SESSION['exito'] = "Profesor actualizado correctamente.";
            header("Location: ../../../vistas/admin/profesores/verProfesores.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el profesor o no hubo cambios.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
    }

    header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesorActualizar");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

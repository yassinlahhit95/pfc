<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $nombreNuevoProfesor = trim($_POST['nombreProfesor']);
    $emailNuevoProfesor = trim($_POST['emailProfesor']);
    $dniNuevoProfesor = trim($_POST['dniProfesor']);
    $telefonoNuevoProfesor = trim($_POST['telefonoProfesor']);
    $direccionNuevoProfesor = trim($_POST['direccionProfesor']);
    
    $fechaNacimientoNuevoProfesor = trim($_POST['fechaNacimientoProfesor'] ?? '1980-01-01');
    $fechaAltaNuevoProfesor = date('Y-m-d');
    $ciudadNuevoProfesor = trim($_POST['ciudadProfesor']);
    $codigoPostalNuevoProfesor = trim($_POST['codigoPostalProfesor']);
    $observacionesNuevoProfesor = trim($_POST['observacionesProfesor']);

    $errores = [];

    if (empty($nombreNuevoProfesor)) {
        $errores['nombreProfesor'] = "El nombre es obligatorio.";
    }
    if (empty($emailNuevoProfesor)) {
        $errores['emailProfesor'] = "El email es obligatorio.";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $emailNuevoProfesor)) {
        $errores['emailProfesor'] = "El formato del email no es válido.";
    }
    if (empty($dniNuevoProfesor)) {
        $errores['dniProfesor'] = "El DNI es obligatorio.";
    }
    if (empty($telefonoNuevoProfesor)) {
        $errores['telefonoProfesor'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefonoNuevoProfesor)) {
        $errores['telefonoProfesor'] = "El teléfono debe ser numérico.";
    }
    if (empty($direccionNuevoProfesor)) {
        $errores['direccionProfesor'] = "La dirección es obligatoria.";
    }
    if (!empty($codigoPostalNuevoProfesor) && !is_numeric($codigoPostalNuevoProfesor)) {
        $errores['codigoPostalProfesor'] = "El código postal debe ser numérico.";
    }

    if (empty($errores)) {
        if (checkProfesorExistente($dniNuevoProfesor, $emailNuevoProfesor)) {
            $errores['dniProfesor'] = "El DNI o Email ya están registrados.";
        }
    }

    if (empty($errores)) {
        $idNuevoProfesorInsertado = insertarProfesor($nombreNuevoProfesor, $emailNuevoProfesor, $telefonoNuevoProfesor, $dniNuevoProfesor, $direccionNuevoProfesor, $fechaNacimientoNuevoProfesor, $fechaAltaNuevoProfesor, $ciudadNuevoProfesor, $codigoPostalNuevoProfesor, $observacionesNuevoProfesor);
        
        if ($idNuevoProfesorInsertado) {
            $_SESSION['exito'] = "Profesor registrado correctamente.";
            header("Location: ../../../vistas/admin/profesores/verProfesores.php");
            exit;
        }
        $_SESSION['error'] = "Hubo un problema al registrar el profesor en la base de datos.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
    }

    header("Location: ../../../vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

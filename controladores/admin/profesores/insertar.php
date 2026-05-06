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

    $listaErroresValidacion = [];

    if (empty($nombreNuevoProfesor)) {
        $listaErroresValidacion['nombreProfesor'] = "El nombre es obligatorio.";
    }
    if (empty($emailNuevoProfesor)) {
        $listaErroresValidacion['emailProfesor'] = "El email es obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $emailNuevoProfesor)) {
        $listaErroresValidacion['emailProfesor'] = "El formato del email no es válido.";
    }
    if (empty($dniNuevoProfesor)) {
        $listaErroresValidacion['dniProfesor'] = "El DNI es obligatorio.";
    }
    if (empty($telefonoNuevoProfesor)) {
        $listaErroresValidacion['telefonoProfesor'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefonoNuevoProfesor)) {
        $listaErroresValidacion['telefonoProfesor'] = "El teléfono debe ser numérico.";
    }
    if (empty($direccionNuevoProfesor)) {
        $listaErroresValidacion['direccionProfesor'] = "La dirección es obligatoria.";
    }

    // Comprobamos duplicados
    if (empty($listaErroresValidacion)) {
        if (checkProfesorExistente($dniNuevoProfesor, $emailNuevoProfesor)) {
            $listaErroresValidacion['dniProfesor'] = "El DNI o Email ya están registrados.";
        }
    }

    if (empty($listaErroresValidacion)) {
        $idNuevoProfesorInsertado = insertarProfesor($nombreNuevoProfesor, $emailNuevoProfesor, $telefonoNuevoProfesor, $dniNuevoProfesor, $direccionNuevoProfesor, $fechaNacimientoNuevoProfesor, $fechaAltaNuevoProfesor, $ciudadNuevoProfesor, $codigoPostalNuevoProfesor, $observacionesNuevoProfesor);
        
        if ($idNuevoProfesorInsertado) {
            // ... (rest of logic)
            $_SESSION['exito'] = "Profesor registrado.";
            header("Location: ../../../vistas/admin/profesores/verProfesores.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el profesor.";
    } else {
        $_SESSION['errores'] = $listaErroresValidacion;
        $_SESSION['datos_profesor'] = $_POST;
    }

    header("Location: ../../../vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;



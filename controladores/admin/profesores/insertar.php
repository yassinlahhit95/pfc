<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['guardarProfesor'])) {
    $nombreNuevoProfesor = trim($_POST['nombreProfesor']);
    $emailNuevoProfesor = trim($_POST['emailProfesor']);
    $dniNuevoProfesor = trim($_POST['dniProfesor']);
    $telefonoNuevoProfesor = trim($_POST['telefonoProfesor']);
    $direccionNuevoProfesor = trim($_POST['direccionProfesor']);
    
    $fechaNacimientoNuevoProfesor = trim($_POST['fechaNacimientoProfesor'] ?? '1980-01-01');
    $fechaAltaNuevoProfesor = date('Y-m-d');
    $ciudadNuevoProfesor = trim($_POST['ciudadProfesor'] ?? '');
    $codigoPostalNuevoProfesor = trim($_POST['codigoPostalProfesor'] ?? '');
    $observacionesNuevoProfesor = trim($_POST['observacionesProfesor'] ?? '');

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
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlDni = "SELECT idProfesor FROM profesores WHERE dniProfesor = '" . mysqli_real_escape_string($con, $dniNuevoProfesor) . "'";
        $resDni = mysqli_query($con, $sqlDni);
        if (mysqli_num_rows($resDni) > 0) {
            $listaErroresValidacion['dniProfesor'] = "Este DNI ya está registrado.";
        }

        $sqlEmail = "SELECT idProfesor FROM profesores WHERE emailProfesor = '" . mysqli_real_escape_string($con, $emailNuevoProfesor) . "'";
        $resEmail = mysqli_query($con, $sqlEmail);
        if (mysqli_num_rows($resEmail) > 0) {
            $listaErroresValidacion['emailProfesor'] = "Este Email ya está registrado.";
        }
        mysqli_close($con);
    }

    if (empty($listaErroresValidacion)) {
        $idNuevoProfesorInsertado = insertarProfesor($nombreNuevoProfesor, $emailNuevoProfesor, $telefonoNuevoProfesor, $dniNuevoProfesor, $direccionNuevoProfesor, $fechaNacimientoNuevoProfesor, $fechaAltaNuevoProfesor, $ciudadNuevoProfesor, $codigoPostalNuevoProfesor, $observacionesNuevoProfesor);
        
        if ($idNuevoProfesorInsertado) {
            // Asignar Ciclos
            if (isset($_POST['ciclos']) && is_array($_POST['ciclos'])) {
                foreach ($_POST['ciclos'] as $idCicloParaAsignar) {
                    asociarCicloProfesor($idCicloParaAsignar, $idNuevoProfesorInsertado);
                }
            }

            // Asignar Módulos
            if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
                foreach ($_POST['modulos'] as $idModuloParaAsignar) {
                    asociarModuloProfesor($idModuloParaAsignar, $idNuevoProfesorInsertado);
                }
            }

            $_SESSION['exito'] = "Profesor registrado.";
            header("Location: ../../../vistas/admin/profesores/verProfesores.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Error inesperado al guardar.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaErroresValidacion;
        $_SESSION['datos_profesor'] = $_POST;
    }

    header("Location: ../../../vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;

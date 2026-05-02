<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardarProfesor'])) {
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
        $listaErroresValidacion['nombreProfesor'] = "Vaya, el nombre es obligatorio.";
    }
    if (empty($emailNuevoProfesor)) {
        $listaErroresValidacion['emailProfesor'] = "Vaya, el email es obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $emailNuevoProfesor)) {
        $listaErroresValidacion['emailProfesor'] = "Vaya, el formato del email no es vÃ¡lido.";
    }
    if (empty($dniNuevoProfesor)) {
        $listaErroresValidacion['dniProfesor'] = "Vaya, el DNI es obligatorio.";
    }
    if (empty($telefonoNuevoProfesor)) {
        $listaErroresValidacion['telefonoProfesor'] = "Vaya, el telÃ©fono es obligatorio.";
    } else if (!is_numeric($telefonoNuevoProfesor)) {
        $listaErroresValidacion['telefonoProfesor'] = "Vaya, el telÃ©fono debe ser numÃ©rico.";
    }
    if (empty($direccionNuevoProfesor)) {
        $listaErroresValidacion['direccionProfesor'] = "Vaya, la direcciÃ³n es obligatoria.";
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

            // Asignar MÃ³dulos
            if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
                foreach ($_POST['modulos'] as $idModuloParaAsignar) {
                    asociarModuloProfesor($idModuloParaAsignar, $idNuevoProfesorInsertado);
                }
            }

            $_SESSION['exito'] = "Listo! Profesor registrado y asignado correctamente.";
            header("Location: ../../../vistas/admin/profesores/verProfesores.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Vaya, hubo un error al guardar en la base de datos.";
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

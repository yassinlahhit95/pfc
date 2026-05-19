<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

if (isset($_POST['actualizarProfesor'])) {
    $idProfesorActualizar = $_POST['idProfesor'];
    $nombreProfesorActualizar = trim($_POST['nombreProfesor']);
    $emailProfesorActualizar = trim($_POST['emailProfesor']);
    $dniProfesorActualizar = trim($_POST['dniProfesor']);
    $telefonoProfesorActualizar = trim($_POST['telefonoProfesor']);
    $direccionProfesorActualizar = trim($_POST['direccionProfesor']);
    $fechaNacimientoProfesor = trim($_POST['fechaNacimientoProfesor'] ?? '');
    $fechaAltaProfesor = trim($_POST['fechaAltaProfesor'] ?? '2026-01-01');
    $ciudadProfesor = trim($_POST['ciudadProfesor']);
    $codigoPostalProfesor = trim($_POST['codigoPostalProfesor']);
    $observacionesProfesor = trim($_POST['observacionesProfesor']);

    $errores = [];
    if (empty($nombreProfesorActualizar)) $errores['nombreProfesor'] = "El nombre es obligatorio.";
    if (empty($emailProfesorActualizar)) {
        $errores['emailProfesor'] = "Falta el email";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $emailProfesorActualizar)) {
        $errores['emailProfesor'] = "Email no válido";
    }
    if (empty($dniProfesorActualizar)) $errores['dniProfesor'] = "El DNI es obligatorio.";
    if (empty($telefonoProfesorActualizar)) {
        $errores['telefonoProfesor'] = "Teléfono requerido";
    } else if (!is_numeric($telefonoProfesorActualizar)) {
        $errores['telefonoProfesor'] = "El teléfono debe ser numérico.";
    }
    if (empty($direccionProfesorActualizar)) $errores['direccionProfesor'] = "Dirección obligatoria";
    if (empty($ciudadProfesor)) $errores['ciudadProfesor'] = "La ciudad es obligatoria.";
    if (empty($codigoPostalProfesor)) {
        $errores['codigoPostalProfesor'] = "Falta el código postal";
    } else if (!is_numeric($codigoPostalProfesor)) {
        $errores['codigoPostalProfesor'] = "Código postal incorrecto";
    }
    if (empty($fechaNacimientoProfesor)) $errores['fechaNacimientoProfesor'] = "Falta la fecha de nacimiento";

    if (empty($errores) && checkProfesorExistente($dniProfesorActualizar, $emailProfesorActualizar, $idProfesorActualizar)) {
        $errores['dniProfesor'] = "El DNI o Email ya están registrados por otro profesor.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
        header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesorActualizar");
        exit;
    }

    if (actualizarProfesor($idProfesorActualizar, $nombreProfesorActualizar, $emailProfesorActualizar, $telefonoProfesorActualizar, $dniProfesorActualizar, $direccionProfesorActualizar, $fechaNacimientoProfesor, $fechaAltaProfesor, $ciudadProfesor, $codigoPostalProfesor, $observacionesProfesor)) {
        limpiarCiclosProfesor($idProfesorActualizar);
        if (isset($_POST['ciclos']) && is_array($_POST['ciclos'])) {
            foreach ($_POST['ciclos'] as $idCic) {
                asociarCicloProfesor($idCic, $idProfesorActualizar);
            }
        }
        limpiarModulosProfesor($idProfesorActualizar);
        if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
            foreach ($_POST['modulos'] as $idMod) {
                asociarModuloProfesor($idMod, $idProfesorActualizar);
            }
        }
        $_SESSION['exito'] = "Profesor actualizado correctamente.";
        header("Location: ../../../vistas/admin/profesores/verProfesores.php");
        exit;
    }
    $_SESSION['error'] = "No se pudo actualizar el profesor o no hubo cambios.";
    header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesorActualizar");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

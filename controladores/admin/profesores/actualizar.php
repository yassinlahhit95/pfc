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

    $errores = '';
    if (empty($nombreProfesorActualizar)) $errores = "El nombre es obligatorio.";
    if (empty($emailProfesorActualizar)) {
        $errores = "Falta el email";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $emailProfesorActualizar)) {
        $errores = "Email no válido";
    }
    if (empty($dniProfesorActualizar)) $errores = "El DNI es obligatorio.";
    if (empty($telefonoProfesorActualizar)) {
        $errores = "Teléfono requerido";
    } else if (!is_numeric($telefonoProfesorActualizar)) {
        $errores = "El teléfono debe ser numérico.";
    }
    if (empty($direccionProfesorActualizar)) $errores = "Dirección obligatoria";
    if (empty($ciudadProfesor)) $errores = "La ciudad es obligatoria.";
    if (empty($codigoPostalProfesor)) {
        $errores = "Falta el código postal";
    } else if (!is_numeric($codigoPostalProfesor)) {
        $errores = "Código postal incorrecto";
    }
    if (empty($fechaNacimientoProfesor)) $errores = "Falta la fecha de nacimiento";

    if (!$errores && checkProfesorExistente($dniProfesorActualizar, $emailProfesorActualizar, $idProfesorActualizar)) {
        $errores = "El DNI o Email ya están registrados por otro profesor.";
    }

    if ($errores) {
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
    $_SESSION['errores'] = "No se pudo actualizar el profesor o no hubo cambios.";
    header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesorActualizar");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

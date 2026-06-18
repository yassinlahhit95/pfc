<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

if (isset($_POST['actualizarProfesor'])) {

    $idProfesorActualizar = (int)($_POST['idProfesor'] ?? 0);
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
        $errores['emailProfesor'] = "El email es obligatorio.";
    } elseif (!Security::validateEmail($emailProfesorActualizar)) {
        $errores['emailProfesor'] = "El formato del email no es válido.";
    }
    if (empty($dniProfesorActualizar)) $errores['dniProfesor'] = "El DNI es obligatorio.";
    if (empty($telefonoProfesorActualizar)) {
        $errores['telefonoProfesor'] = "El teléfono es obligatorio.";
    } elseif (!Security::validatePhone($telefonoProfesorActualizar)) {
        $errores['telefonoProfesor'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";
    }
    if (empty($direccionProfesorActualizar)) $errores['direccionProfesor'] = "La dirección es obligatoria.";
    if (empty($ciudadProfesor)) $errores['ciudadProfesor'] = "La ciudad es obligatoria.";
    if (empty($codigoPostalProfesor)) {
        $errores['codigoPostalProfesor'] = "El código postal es obligatorio.";
    } elseif (!is_numeric($codigoPostalProfesor)) {
        $errores['codigoPostalProfesor'] = "El código postal debe ser numérico.";
    }
    if (empty($fechaNacimientoProfesor)) $errores['fechaNacimientoProfesor'] = "La fecha de nacimiento es obligatoria.";

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
    $_SESSION['errores'] = "No se pudo actualizar el profesor o no hubo cambios.";
    header("Location: ../../../vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesorActualizar");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

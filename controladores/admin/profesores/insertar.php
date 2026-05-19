<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $nombreNuevoProfesor = trim($_POST['nombreProfesor']);
    $emailNuevoProfesor = trim($_POST['emailProfesor']);
    $dniNuevoProfesor = trim($_POST['dniProfesor']);
    $telefonoNuevoProfesor = trim($_POST['telefonoProfesor']);
    $direccionNuevoProfesor = trim($_POST['direccionProfesor']);
    $fechaNacimientoNuevoProfesor = trim($_POST['fechaNacimientoProfesor'] ?? '');
    $fechaAltaNuevoProfesor = date('Y-m-d');
    $ciudadNuevoProfesor = trim($_POST['ciudadProfesor']);
    $codigoPostalNuevoProfesor = trim($_POST['codigoPostalProfesor']);
    $observacionesNuevoProfesor = trim($_POST['observacionesProfesor']);

    $errores = [];
    if (empty($nombreNuevoProfesor)) $errores['nombreProfesor'] = "Nombre obligatorio";
    if (empty($emailNuevoProfesor)) {
        $errores['emailProfesor'] = "El email es obligatorio.";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $emailNuevoProfesor)) {
        $errores['emailProfesor'] = "Email inválido";
    }
    if (empty($dniNuevoProfesor)) $errores['dniProfesor'] = "Falta el DNI";
    if (empty($telefonoNuevoProfesor)) {
        $errores['telefonoProfesor'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefonoNuevoProfesor)) {
        $errores['telefonoProfesor'] = "Solo números";
    }
    if (empty($direccionNuevoProfesor)) $errores['direccionProfesor'] = "La dirección es obligatoria.";
    if (empty($ciudadNuevoProfesor)) $errores['ciudadProfesor'] = "Ciudad requerida";
    if (empty($codigoPostalNuevoProfesor)) {
        $errores['codigoPostalProfesor'] = "Falta el código postal";
    } else if (!is_numeric($codigoPostalNuevoProfesor)) {
        $errores['codigoPostalProfesor'] = "Código postal incorrecto";
    }
    if (empty($fechaNacimientoNuevoProfesor)) $errores['fechaNacimientoProfesor'] = "La fecha de nacimiento es obligatoria.";

    if (empty($errores) && checkProfesorExistente($dniNuevoProfesor, $emailNuevoProfesor)) {
        $errores['dniProfesor'] = "El DNI o Email ya están registrados.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
        header("Location: ../../../vistas/admin/profesores/agregarProfesores.php");
        exit;
    }

    $idNuevoProfesorInsertado = insertarProfesor($nombreNuevoProfesor, $emailNuevoProfesor, $telefonoNuevoProfesor, $dniNuevoProfesor, $direccionNuevoProfesor, $fechaNacimientoNuevoProfesor, $fechaAltaNuevoProfesor, $ciudadNuevoProfesor, $codigoPostalNuevoProfesor, $observacionesNuevoProfesor);

    if ($idNuevoProfesorInsertado) {
        if (isset($_POST['ciclos']) && is_array($_POST['ciclos'])) {
            foreach ($_POST['ciclos'] as $idCic) {
                asociarCicloProfesor($idCic, $idNuevoProfesorInsertado);
            }
        }
        if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
            foreach ($_POST['modulos'] as $idMod) {
                asociarModuloProfesor($idMod, $idNuevoProfesorInsertado);
            }
        }
        $_SESSION['exito'] = "Profesor registrado correctamente.";
        header("Location: ../../../vistas/admin/profesores/verProfesores.php");
        exit;
    }
    $_SESSION['error'] = "Hubo un problema al registrar el profesor en la base de datos.";
    header("Location: ../../../vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

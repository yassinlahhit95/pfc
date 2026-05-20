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

    $errores = '';
    if (empty($nombreNuevoProfesor)) $errores = "Nombre obligatorio";
    if (empty($emailNuevoProfesor)) {
        $errores = "El email es obligatorio.";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $emailNuevoProfesor)) {
        $errores = "Email inválido";
    }
    if (empty($dniNuevoProfesor)) $errores = "Falta el DNI";
    if (empty($telefonoNuevoProfesor)) {
        $errores = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefonoNuevoProfesor)) {
        $errores = "Solo números";
    }
    if (empty($direccionNuevoProfesor)) $errores = "La dirección es obligatoria.";
    if (empty($ciudadNuevoProfesor)) $errores = "Ciudad requerida";
    if (empty($codigoPostalNuevoProfesor)) {
        $errores = "Falta el código postal";
    } else if (!is_numeric($codigoPostalNuevoProfesor)) {
        $errores = "Código postal incorrecto";
    }
    if (empty($fechaNacimientoNuevoProfesor)) $errores = "La fecha de nacimiento es obligatoria.";

    if (!$errores && checkProfesorExistente($dniNuevoProfesor, $emailNuevoProfesor)) {
        $errores = "El DNI o Email ya están registrados.";
    }

    if ($errores) {
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
    $_SESSION['errores'] = "Hubo un problema al registrar el profesor en la base de datos.";
    header("Location: ../../../vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>

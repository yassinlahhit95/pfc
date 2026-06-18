<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
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
    if (empty($nombreNuevoProfesor)) $errores['nombreProfesor'] = "El nombre es obligatorio.";
    if (empty($emailNuevoProfesor)) {
        $errores['emailProfesor'] = "El email es obligatorio.";
    } elseif (!Security::validateEmail($emailNuevoProfesor)) {
        $errores['emailProfesor'] = "El formato del email no es válido.";
    }
    if (empty($dniNuevoProfesor)) $errores['dniProfesor'] = "El DNI es obligatorio.";
    if (empty($telefonoNuevoProfesor)) {
        $errores['telefonoProfesor'] = "El teléfono es obligatorio.";
    } elseif (!Security::validatePhone($telefonoNuevoProfesor)) {
        $errores['telefonoProfesor'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";
    }
    if (empty($direccionNuevoProfesor)) $errores['direccionProfesor'] = "La dirección es obligatoria.";
    if (empty($ciudadNuevoProfesor)) $errores['ciudadProfesor'] = "La ciudad es obligatoria.";
    if (empty($codigoPostalNuevoProfesor)) {
        $errores['codigoPostalProfesor'] = "El código postal es obligatorio.";
    } elseif (!is_numeric($codigoPostalNuevoProfesor)) {
        $errores['codigoPostalProfesor'] = "El código postal debe ser numérico.";
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
                $idCic = (int)$idCic;
                if ($idCic > 0) asociarCicloProfesor($idCic, $idNuevoProfesorInsertado);
            }
        }
        if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
            foreach ($_POST['modulos'] as $idMod) {
                $idMod = (int)$idMod;
                if ($idMod > 0) asociarModuloProfesor($idMod, $idNuevoProfesorInsertado);
            }
        }
        $_SESSION['exito'] = mensajeExitoConCredenciales("Profesor registrado correctamente.");
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

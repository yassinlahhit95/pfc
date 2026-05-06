<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['guardarEstudiante'])) {
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $fechaAlta = trim($_POST['fechaAltaEstudiante']);
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = trim($_POST['observacionesEstudiante']);
    $idCiclo = trim($_POST['idCiclo']);

    $errores = [];

    if (empty($nombre)) {
        $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errores['emailEstudiante'] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['emailEstudiante'] = "El formato del email no es vÃ¡lido.";
    }
    if (empty($dni)) {
        $errores['dniEstudiante'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El telÃ©fono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores['telefonoEstudiante'] = "El telÃ©fono debe ser numÃ©rico y tener exactamente 9 dÃ­gitos.";
    }
    if (empty($fechaNacimiento)) {
        $errores['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria.";
    }
    if (empty($direccion)) {
        $errores['direccionEstudiante'] = "La direcciÃ³n es obligatoria.";
    }
    if (empty($ciudad)) {
        $errores['ciudadEstudiante'] = "La ciudad es obligatoria.";
    }
    if (empty($codigoPostal)) {
        $errores['codigoPostalEstudiante'] = "El cÃ³digo postal es obligatorio.";
    }
    if (empty($idCiclo)) {
        $errores['idCiclo'] = "Debe seleccionar un ciclo.";
    }

    // Comprobamos duplicados antes de insertar
    if (empty($errores)) {
        if (checkEstudianteExistente($dni, $email)) {
            $errores['dniEstudiante'] = "El DNI o Email ya están registrados.";
        }
    }

    if (empty($errores)) {
        $resultado = insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante registrado.";
            header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        }
        $_SESSION['error'] = "Hubo un problema técnico al intentar guardar el estudiante en la base de datos.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: ../../../vistas/admin/estudiantes/agregarEstudiantes.php");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;



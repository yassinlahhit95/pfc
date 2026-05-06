<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
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

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

    $errores = [];

    if (empty($nombre)) {
        $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errores['emailEstudiante'] = "El email es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['emailEstudiante'] = "El formato del email no es válido.";
    }
    if (empty($dni)) {
        $errores['dniEstudiante'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono debe ser numérico y tener exactamente 9 dígitos.";
    }
    if (empty($idCiclo)) {
        $errores['idCiclo'] = "Debe seleccionar un ciclo.";
    }

    // Comprobamos duplicados antes de actualizar
    if (empty($errores)) {
        if (checkEstudianteExistente($dni, $email, $idEstudiante)) {
            $errores['dniEstudiante'] = "El DNI o Email ya están registrados por otro estudiante.";
        }
    }

    if (empty($errores)) {
        $resultado = actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante actualizado.";
            header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        }
        $_SESSION['error'] = "Hubo un problema técnico al intentar actualizar los datos del estudiante.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: ../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;



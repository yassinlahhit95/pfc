<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../login.php");
    exit;
}

if (isset($_POST['guardarEstudiante'])) {
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $fechaAlta = date('Y-m-d');
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = isset($_POST['observacionesEstudiante']) ? trim($_POST['observacionesEstudiante']) : '';
    $idCiclo = trim($_POST['idCiclo']);
    $curso = isset($_POST['curso']) ? trim($_POST['curso']) : 'Grado Medio';

    $errores = [];

    if (empty($nombre)) {
        $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errores['emailEstudiante'] = "El email es obligatorio.";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores['emailEstudiante'] = "El formato del email no es válido.";
    }
    if (empty($dni)) {
        $errores['dniEstudiante'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono es obligatorio.";
    }
    if (empty($fechaNacimiento)) {
        $errores['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria.";
    }
    if (empty($idCiclo)) {
        $errores['idCiclo'] = "Debe seleccionar un ciclo.";
    }

    if (empty($errores)) {
        if (checkEstudianteExistente($dni, $email)) {
            $errores['dniEstudiante'] = "El DNI o Email ya están registrados.";
        }
    }

    if (empty($errores)) {
        $resultado = insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante registrado correctamente.";
            header("Location: ../../../vistas/profesores/estudiantes/lista.php");
            exit;
        }
        $_SESSION['error'] = "Hubo un problema al intentar guardar el estudiante.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: ../../../vistas/profesores/estudiantes/agregar.php");
    exit;
}

header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
?>

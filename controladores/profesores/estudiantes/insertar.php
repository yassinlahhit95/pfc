<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../vistas/login.php");
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
    $idCiclo = $_POST['idCiclo'];
    $curso = $_POST['curso'] ?? 'Grado Medio';

    $avisos = [];
    if (empty($nombre)) $avisos['nombreEstudiante'] = "El nombre es obligatorio.";
    if (empty($email)) {
        $avisos['emailEstudiante'] = "Falta el email";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $avisos['emailEstudiante'] = "Email no válido";
    }
    if (empty($dni)) $avisos['dniEstudiante'] = "El DNI es obligatorio.";
    if (empty($telefono)) $avisos['telefonoEstudiante'] = "Teléfono requerido";
    if (empty($fechaNacimiento)) $avisos['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria.";
    if (empty($idCiclo)) $avisos['idCiclo'] = "Selecciona un ciclo";

    if (empty($avisos) && checkEstudianteExistente($dni, $email)) {
        $avisos['dniEstudiante'] = "El DNI o Email ya están registrados.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_estudiante'] = $_POST;
        header("Location: ../../../vistas/profesores/estudiantes/agregar.php");
        exit;
    }

    if (insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso)) {
        $_SESSION['exito'] = "Estudiante registrado correctamente.";
        header("Location: ../../../vistas/profesores/estudiantes/lista.php");
        exit;
    }
    $_SESSION['errores'] = "Hubo un problema al intentar guardar el estudiante.";
    header("Location: ../../../vistas/profesores/estudiantes/agregar.php");
    exit;
}

header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
?>

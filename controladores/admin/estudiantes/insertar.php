<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/Security.php";

if (isset($_POST['guardarEstudiante'])) {
    // Validar CSRF
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../../vistas/admin/estudiantes/agregarEstudiantes.php'));
        exit;
    }

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
    $idCiclo = $_POST['idCiclo'];
    $curso = $_POST['curso'] ?? '';

    $avisos = [];
    if (empty($nombre)) $avisos['nombreEstudiante'] = "El nombre es obligatorio.";
    if (empty($email)) {
        $avisos['emailEstudiante'] = "Email requerido";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $avisos['emailEstudiante'] = "Email no válido";
    }
    if (empty($dni)) $avisos['dniEstudiante'] = "Falta el DNI";
    if (empty($telefono)) {
        $avisos['telefonoEstudiante'] = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $avisos['telefonoEstudiante'] = "Teléfono incorrecto";
    }
    if (empty($fechaNacimiento)) $avisos['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria.";
    if (empty($direccion)) $avisos['direccionEstudiante'] = "Dirección requerida";
    if (empty($ciudad)) $avisos['ciudadEstudiante'] = "La ciudad es obligatoria.";
    if (empty($codigoPostal)) {
        $avisos['codigoPostalEstudiante'] = "El código postal es obligatorio.";
    } elseif (!is_numeric($codigoPostal)) {
        $avisos['codigoPostalEstudiante'] = "Código postal no válido";
    }
    if (empty($curso)) $avisos['curso'] = "Selecciona un grado";
    if (empty($idCiclo)) $avisos['idCiclo'] = "Selecciona un ciclo";

    if (empty($avisos) && checkEstudianteExistente($dni, $email)) {
        $avisos['dniEstudiante'] = "El DNI o Email ya están registrados.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_estudiante'] = $_POST;
        header("Location: ../../../vistas/admin/estudiantes/agregarEstudiantes.php");
        exit;
    }

    if (insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso)) {
        $_SESSION['exito'] = "Estudiante registrado correctamente.";
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }
    $_SESSION['errores'] = "Hubo un problema al intentar guardar el estudiante en la base de datos.";
    header("Location: ../../../vistas/admin/estudiantes/agregarEstudiantes.php");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>

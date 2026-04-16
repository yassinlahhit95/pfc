<?php
session_start();
require_once "../../modelos/estudiantes.php";

if (isset($_POST['guardarEstudiante'])) {
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_estudiante']);

    $nombre = trim($_POST['nombreEstudiante'] ?? '');
    $email = trim($_POST['emailEstudiante'] ?? '');
    $telefono = trim($_POST['telefonoEstudiante'] ?? '');
    $fechaNac = $_POST['fechaNacimientoEstudiante'] ?? '';
    $dni = trim($_POST['dniEstudiante'] ?? '');
    $fechaAlta = $_POST['fechaAltaEstudiante'] ?? date('Y-m-d');
    $direccion = trim($_POST['direccionEstudiante'] ?? '');
    $ciudad = trim($_POST['ciudadEstudiante'] ?? '');
    $cp = trim($_POST['codigoPostalEstudiante'] ?? '');
    $obs = trim($_POST['observacionesEstudiante'] ?? '');
    $idCiclo = $_POST['idCiclo'] ?? '';
    $idEstado = $_POST['idEstado'] ?? '';
    
    $errores = [];

    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre del estudiante es obligatorio";
    if (empty($email)) $errores['emailEstudiante'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniEstudiante'] = "El DNI es obligatorio";
    
    if (empty($idCiclo)) {
        $errores['idCiclo'] = "El ciclo es obligatorio";
    } elseif (!is_numeric($idCiclo) || !preg_match('/^[0-9]+$/', $idCiclo) || !ctype_digit($idCiclo)) {
        $errores['idCiclo'] = "El ciclo debe ser un número entero válido";
    }

    if (empty($idEstado)) {
        $errores['idEstado'] = "El estado es obligatorio";
    } elseif (!is_numeric($idEstado) || !preg_match('/^[0-9]+$/', $idEstado) || !ctype_digit($idEstado)) {
        $errores['idEstado'] = "El estado debe ser un número entero válido";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
        exit;
    }

    $modelo = new estudiante();
    if ($modelo->insertarEstudianteModelo($nombre, $email, $telefono, $fechaNac, $dni, $fechaAlta, $direccion, $ciudad, $cp, $obs, $idCiclo, $idEstado)) {
        $_SESSION['exito'] = "Estudiante registrado correctamente";
    } else {
        $_SESSION['error'] = "Error al registrar el estudiante";
    }

    header("Location: ../../vistas/estudiantes/verEstudiantes.php");
    exit;
}
?>

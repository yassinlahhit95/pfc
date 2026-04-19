<?php
session_start();
require_once "../../modelos/estudiantes.php";

if (isset($_POST['guardarEstudiante'])) {
    $nombre = trim($_POST['nombreEstudiante'] ?? '');
    $email = trim($_POST['emailEstudiante'] ?? '');
    $dni = trim($_POST['dniEstudiante'] ?? '');
    $telefono = trim($_POST['telefonoEstudiante'] ?? '');
    $fNac = $_POST['fechaNacimientoEstudiante'] ?? '';
    $fAlta = $_POST['fechaAltaEstudiante'] ?? date('Y-m-d');
    $dir = trim($_POST['direccionEstudiante'] ?? '');
    $ciu = trim($_POST['ciudadEstudiante'] ?? '');
    $cp = trim($_POST['codigoPostalEstudiante'] ?? '');
    $obs = trim($_POST['observacionesEstudiante'] ?? '');
    $idCiclo = $_POST['idCiclo'] ?? '';

    // Guardamos datos para no perder el formulario
    $_SESSION['datos_estudiante'] = $_POST;

    if (empty($nombre) || empty($email) || empty($dni)) {
        $_SESSION['error'] = "Nombre, Email y DNI son obligatorios.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
        exit;
    }

    if (insertarEstudiante($nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo)) {
        unset($_SESSION['datos_estudiante']);
        $_SESSION['exito'] = "Estudiante registrado con éxito.";
        header("Location: ../../vistas/estudiantes/verEstudiantes.php");
    } else {
        $_SESSION['error'] = "Error al guardar el estudiante en la base de datos.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    }
    exit;
}

header("Location: ../../vistas/estudiantes/verEstudiantes.php");
exit;
?>

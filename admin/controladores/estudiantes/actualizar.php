<?php
session_start();
require_once "../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {
    $id = $_POST['idEstudiante'];
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

    if (empty($id)) {
        header("Location: ../../vistas/estudiantes/verEstudiantes.php");
        exit;
    }

    if (empty($nombre) || empty($email) || empty($dni)) {
        $_SESSION['error'] = "Nombre, Email y DNI son obligatorios.";
        header("Location: ../../vistas/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
        exit;
    }

    if (actualizarEstudiante($id, $nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo)) {
        $_SESSION['exito'] = "Estudiante actualizado correctamente.";
        header("Location: ../../vistas/estudiantes/verEstudiantes.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el estudiante en la base de datos.";
        header("Location: ../../vistas/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    }
    exit;
}

header("Location: ../../vistas/estudiantes/verEstudiantes.php");
exit;
?>

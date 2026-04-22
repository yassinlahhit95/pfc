<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $nombre = trim($_POST['nombreEstudiante']);
    $email = strtolower(trim($_POST['emailEstudiante']));
    $dni = strtoupper(trim($_POST['dniEstudiante']));
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = $_POST['fechaNacimientoEstudiante'];
    $fechaAlta = $_POST['fechaAltaEstudiante'];
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = trim($_POST['observacionesEstudiante']);
    $idCiclo = $_POST['idCiclo'];

    if (empty($idEstudiante)) {
        header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
    } else if (!is_numeric($telefono) && !empty($telefono)) {
        $_SESSION['error'] = "El teléfono debe ser numérico.";
    } else if (actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo)) {
        $_SESSION['exito'] = "Actualizado correctamente.";
        header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al actualizar.";
    }
    header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
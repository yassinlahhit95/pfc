<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $idProfesor = $_POST['idProfesor'];
    $nombre = trim($_POST['nombreProfesor']);
    $email = strtolower(trim($_POST['emailProfesor']));
    $dni = strtoupper(trim($_POST['dniProfesor']));
    $telefono = trim($_POST['telefonoProfesor']);
    $especialidad = trim($_POST['especialidad']);
    $direccion = trim($_POST['direccionProfesor']);

    if (empty($idProfesor)) {
        header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
        exit;
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
    } else if (!is_numeric($telefono) && !empty($telefono)) {
        $_SESSION['error'] = "El teléfono debe ser numérico.";
    } else if (actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $especialidad, $direccion)) {
        $_SESSION['exito'] = "Profesor actualizado correctamente.";
        header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al actualizar.";
    }
    header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$idProfesor");
    exit;
}

header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
exit;
?>
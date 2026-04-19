<?php
session_start();
require_once "../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $id = $_POST['idProfesor'];
    $nombre = trim($_POST['nombreProfesor'] ?? '');
    $email = trim($_POST['emailProfesor'] ?? '');
    $dni = trim($_POST['dniProfesor'] ?? '');
    $telefono = trim($_POST['telefonoProfesor'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $direccion = trim($_POST['direccionProfesor'] ?? '');

    if (empty($id)) {
        header("Location: ../../vistas/profesores/verProfesores.php");
        exit;
    }

    if (empty($nombre) || empty($email) || empty($dni)) {
        $_SESSION['error'] = "Nombre, Email y DNI son obligatorios.";
        header("Location: ../../vistas/profesores/modificarProfesores.php?idProfesor=$id");
        exit;
    }

    if (actualizarProfesor($id, $nombre, $email, $telefono, $dni, $especialidad, $direccion)) {
        $_SESSION['exito'] = "Profesor actualizado correctamente.";
        header("Location: ../../vistas/profesores/verProfesores.php");
    } else {
        $_SESSION['error'] = "Error al actualizar el profesor en la base de datos.";
        header("Location: ../../vistas/profesores/modificarProfesores.php?idProfesor=$id");
    }
    exit;
}

header("Location: ../../vistas/profesores/verProfesores.php");
exit;
?>

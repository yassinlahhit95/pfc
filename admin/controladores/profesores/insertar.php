<?php
session_start();
require_once "../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $nombre = trim($_POST['nombreProfesor'] ?? '');
    $email = trim($_POST['emailProfesor'] ?? '');
    $dni = trim($_POST['dniProfesor'] ?? '');
    $telefono = trim($_POST['telefonoProfesor'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $direccion = trim($_POST['direccionProfesor'] ?? '');

    // Guardamos datos para no perder el formulario
    $_SESSION['datos_profesor'] = $_POST;

    if (empty($nombre) || empty($email) || empty($dni)) {
        $_SESSION['error'] = "Nombre, Email y DNI son obligatorios.";
        header("Location: ../../vistas/profesores/agregarProfesores.php");
        exit;
    }

    if (insertarProfesor($nombre, $email, $telefono, $dni, $especialidad, $direccion)) {
        unset($_SESSION['datos_profesor']);
        $_SESSION['exito'] = "Profesor registrado con éxito.";
        header("Location: ../../vistas/profesores/verProfesores.php");
    } else {
        $_SESSION['error'] = "Error al guardar el profesor en la base de datos.";
        header("Location: ../../vistas/profesores/agregarProfesores.php");
    }
    exit;
}

header("Location: ../../vistas/profesores/verProfesores.php");
exit;
?>

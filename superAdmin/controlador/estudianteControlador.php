<?php
session_start();
require_once "../modelo/conexion.php";
require_once "../modelo/estudiantes.php";

$errors = [];
$old = [];

if (isset($_POST['submit'])) {

    $old = $_POST; // نحافظ على جميع القيم القديمة

    // Validaciones
    if (empty($_POST['nombreEstudiante'])) {
        $errors['nombreEstudiante'] = "El nombre es obligatorio";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $_POST['nombreEstudiante'])) {
        $errors['nombreEstudiante'] = "El nombre no es válido";
    }

    if (empty($_POST['emailEstudiante'])) {
        $errors['emailEstudiante'] = "El correo electrónico es obligatorio";
    } elseif (!filter_var($_POST['emailEstudiante'], FILTER_VALIDATE_EMAIL)) {
        $errors['emailEstudiante'] = "El correo electrónico no es válido";
    }

    if (!empty($_POST['telefonoEstudiante']) && !preg_match("/^\+?[0-9]+$/", $_POST['telefonoEstudiante'])) {
        $errors['telefonoEstudiante'] = "El teléfono no es válido, solo puede empezar con +";
    }

    if (empty($_POST['fechaAltaEstudiante'])) {
        $errors['fechaAltaEstudiante'] = "La fecha de ingreso es obligatoria";
    }

    if (empty($_POST['fechaNacimientoEstudiante'])) {
        $errors['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria";
    } elseif (strtotime($_POST['fechaNacimientoEstudiante']) > time()) {
        $errors['fechaNacimientoEstudiante'] = "La fecha de nacimiento no puede ser futura";
    }

    if (empty($_POST['idCurso']) || !is_numeric($_POST['idCurso'])) {
        $errors['idCurso'] = "Debe seleccionar un curso";
    }

    if (empty($_POST['idEstado']) || !is_numeric($_POST['idEstado'])) {
        $errors['idEstado'] = "Debe seleccionar un estado";
    }

    if (empty($errors)) {
        $conexion = new Conexion();
        $db = $conexion->conectar();

        $estudiante = new estudiante($db);
        $estudiante->insertarEstudianteModelo($_POST);

        unset($_SESSION['errors'], $_SESSION['old']);
        header("Location: ../vistas/estudiantes/verEstudiantes.php");
        exit;
    }

    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    header("Location: ../vistas/estudiantes/agregarEstudiantes.php");
    exit;
}
?>
<?php
session_start();
require_once "../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    unset($_SESSION['errores']);
    unset($_SESSION['datos_profesor']);

    $id = $_POST['idProfesor'] ?? '';
    $nombre = trim($_POST['nombreProfesor'] ?? '');
    $email = trim($_POST['emailProfesor'] ?? '');
    $dni = trim($_POST['dniProfesor'] ?? '');
    $telefono = trim($_POST['telefonoProfesor'] ?? '');
    $especialidad = trim($_POST['especialidad'] ?? '');
    $direccion = trim($_POST['direccionProfesor'] ?? '');
    $idEstado = $_POST['idEstado'] ?? 1;
    
    $errores = [];

    if (empty($id)) {
        $errores['general'] = "ID del profesor no válido";
    } elseif (!is_numeric($id) || !preg_match('/^[0-9]+$/', $id) || !ctype_digit($id)) {
        $errores['general'] = "ID del profesor debe ser un número entero válido";
    }

    if (empty($nombre)) $errores['nombreProfesor'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailProfesor'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniProfesor'] = "El DNI es obligatorio";
    if (empty($telefono)) $errores['telefonoProfesor'] = "El teléfono es obligatorio";
    if (empty($especialidad)) $errores['especialidad'] = "La especialidad es obligatoria";
    if (empty($direccion)) $errores['direccionProfesor'] = "La dirección es obligatoria";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_profesor'] = $_POST;
        header("Location: ../../vistas/profesores/modificarProfesores.php?id=$id");
        exit;
    }

    $modelo = new profesor();
    if ($modelo->actualizarProfesoresModelo($id, $nombre, $email, $telefono, $dni, $especialidad, $direccion, $idEstado)) {
        $_SESSION['exito'] = "Profesor actualizado correctamente";
    } else {
        $_SESSION['error'] = "Error al actualizar el profesor";
    }

    header("Location: ../../vistas/profesores/verProfesores.php");
    exit;
}
?>

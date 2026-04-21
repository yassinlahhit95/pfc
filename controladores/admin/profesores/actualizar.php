<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $id = $_POST['idProfesor'];
    $nombre = trim($_POST['nombreProfesor']);
    $email = trim($_POST['emailProfesor']);
    $dni = trim($_POST['dniProfesor']);
    $telefono = trim($_POST['telefonoProfesor']);
    $especialidad = trim($_POST['especialidad']);
    $direccion = trim($_POST['direccionProfesor']);

    // Regex
    $regexEmail = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $regexTelefono = "/^[0-9]{9}$/";

    if (empty($id)) {
        header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$id");
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
        header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$id");
    } else if (!preg_match($regexEmail, $email)) {
        $_SESSION['error'] = "El formato del email no es válido.";
        header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$id");
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
        header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$id");
    } else if (!empty($telefono) && !preg_match($regexTelefono, $telefono)) {
        $_SESSION['error'] = "El teléfono debe tener exactamente 9 números.";
        header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$id");
    } else {
        if (actualizarProfesor($id, $nombre, $email, $telefono, $dni, $especialidad, $direccion)) {
            $_SESSION['exito'] = "Profesor actualizado correctamente.";
            header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
        } else {
            $_SESSION['error'] = "Error al actualizar el profesor en la base de datos.";
            header("Location: /pfc/vistas/admin/profesores/modificarProfesores.php?idProfesor=$id");
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
exit;
?>
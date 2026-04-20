<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['guardarEstudiante'])) {
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fNac = $_POST['fechaNacimientoEstudiante'];
    $fAlta = $_POST['fechaAltaEstudiante'];
    $dir = trim($_POST['direccionEstudiante']);
    $ciu = trim($_POST['ciudadEstudiante']);
    $cp = trim($_POST['codigoPostalEstudiante']);
    $obs = trim($_POST['observacionesEstudiante']);
    $idCiclo = $_POST['idCiclo'];

    // Regex
    $regexEmail = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $regexTelefono = "/^[0-9]{9}$/";
    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else if (!preg_match($regexEmail, $email)) {
        $_SESSION['error'] = "El formato del email no es válido.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else if (!empty($telefono) && !preg_match($regexTelefono, $telefono)) {
        $_SESSION['error'] = "El teléfono debe tener exactamente 9 números.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else if (!empty($fNac) && !preg_match($regexFecha, $fNac)) {
        $_SESSION['error'] = "La fecha de nacimiento debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else if (!empty($fAlta) && !preg_match($regexFecha, $fAlta)) {
        $_SESSION['error'] = "La fecha de alta debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
    } else {
        if (insertarEstudiante($nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo)) {
            $_SESSION['exito'] = "Estudiante registrado con éxito.";
            header("Location: ../../vistas/estudiantes/verEstudiantes.php");
        } else {
            $_SESSION['error'] = "Error al guardar el estudiante en la base de datos.";
            header("Location: ../../vistas/estudiantes/agregarEstudiantes.php");
        }
    }
    exit;
}

header("Location: ../../vistas/estudiantes/verEstudiantes.php");
exit;
?>
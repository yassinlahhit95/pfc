<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {
    $id = $_POST['idEstudiante'];
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
    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/"; // Formato estándar HTML5: YYYY-MM-DD

    if (empty($id)) {
        header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else if (!preg_match($regexEmail, $email)) {
        $_SESSION['error'] = "El formato del email no es válido.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else if (!empty($telefono) && !preg_match($regexTelefono, $telefono)) {
        $_SESSION['error'] = "El teléfono debe tener exactamente 9 números.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else if (!empty($fNac) && !preg_match($regexFecha, $fNac)) {
        $_SESSION['error'] = "La fecha de nacimiento no es válida.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else if (!empty($fAlta) && !preg_match($regexFecha, $fAlta)) {
        $_SESSION['error'] = "La fecha de alta no es válida.";
        header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
    } else {
        // Al usar <input type="date">, el valor ya viene como YYYY-MM-DD,
        // por lo que podemos usar las variables directamente en la BD.

        if (actualizarEstudiante($id, $nombre, $email, $telefono, $fNac, $dni, $fAlta, $dir, $ciu, $cp, $obs, $idCiclo)) {
            $_SESSION['exito'] = "Estudiante actualizado correctamente.";
            header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
        } else {
            $_SESSION['error'] = "Error al actualizar el estudiante en la base de datos.";
            header("Location: /pfc/vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$id");
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
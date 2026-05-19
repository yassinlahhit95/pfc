<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $fechaAlta = trim($_POST['fechaAltaEstudiante']);
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = trim($_POST['observacionesEstudiante']);
    $idCiclo = $_POST['idCiclo'];
    $curso = $_POST['curso'] ?? 'Grado Medio';

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

    $errores = [];
    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    if (empty($email)) {
        $errores['emailEstudiante'] = "Email obligatorio";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores['emailEstudiante'] = "Formato de email no válido";
    }
    if (empty($dni)) $errores['dniEstudiante'] = "DNI obligatorio";
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores['telefonoEstudiante'] = "9 dígitos, solo números";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $errores['codigoPostalEstudiante'] = "Código postal incorrecto";
    }
    if (empty($idCiclo)) $errores['idCiclo'] = "Selecciona el ciclo";

    if (empty($errores) && checkEstudianteExistente($dni, $email, $idEstudiante)) {
        $errores['dniEstudiante'] = "El DNI o Email ya están registrados por otro estudiante.";
    }

    if (empty($errores)) {
        if (actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso)) {
            $_SESSION['exito'] = "Datos del estudiante actualizados correctamente.";
            header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        }
        $_SESSION['error'] = "Hay un problema al intentar actualizar los datos del estudiante.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: ../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>

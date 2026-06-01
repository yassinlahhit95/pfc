<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/Security.php";

if (isset($_POST['actualizarEstudiante'])) {
    // Validar CSRF
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../../vistas/admin/estudiantes/verEstudiantes.php'));
        exit;
    }

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

    $errores = '';
    if (empty($nombre)) $errores = "El nombre es obligatorio.";
    if (empty($email)) {
        $errores = "Email obligatorio";
    } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores = "Formato de email no válido";
    }
    if (empty($dni)) $errores = "DNI obligatorio";
    if (empty($telefono)) {
        $errores = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores = "9 dígitos, solo números";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $errores = "Código postal incorrecto";
    }
    if (empty($idCiclo)) $errores = "Selecciona el ciclo";

    if (!$errores && checkEstudianteExistente($dni, $email, $idEstudiante)) {
        $errores = "El DNI o Email ya están registrados por otro estudiante.";
    }

    if (!$errores) {
        if (actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso)) {
            $_SESSION['exito'] = "Datos del estudiante actualizados correctamente.";
            header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
            exit;
        }
        $_SESSION['errores'] = "Hay un problema al intentar actualizar los datos del estudiante.";
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

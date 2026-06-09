<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

if (isset($_POST['actualizarEstudiante'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $dni = trim($_POST['dniEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);
    $fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
    $direccion = trim($_POST['direccionEstudiante']);
    $ciudad = trim($_POST['ciudadEstudiante']);
    $codigoPostal = trim($_POST['codigoPostalEstudiante']);
    $observaciones = isset($_POST['observacionesEstudiante']) ? trim($_POST['observacionesEstudiante']) : '';
    $idCiclo = trim($_POST['idCiclo']);

    $errores = '';

    if (empty($nombre)) { $errores = "El nombre es obligatorio."; }
    if (empty($email)) { $errores = "El email es obligatorio."; }
    if (empty($dni)) { $errores = "El DNI es obligatorio."; }
    if (empty($idCiclo)) { $errores = "Debe seleccionar un ciclo."; }

    if (!$errores) {
        if (checkEstudianteExistente($dni, $email, $idEstudiante)) {
            $errores = "El DNI o Email ya están registrados.";
        }
    }

    if (!$errores) {
        $estudianteOriginal = obtenerEstudiantePorId($idEstudiante);
        $fechaAlta = $estudianteOriginal['fechaAltaEstudiante'];

        $resultado = actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante actualizado correctamente.";
            header("Location: ../../../vistas/profesores/estudiantes/lista.php");
            exit;
        }
        $_SESSION['errores'] = "Hubo un problema al intentar actualizar el estudiante.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_estudiante'] = $_POST;
    }

    header("Location: ../../../vistas/profesores/estudiantes/editar.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
?>

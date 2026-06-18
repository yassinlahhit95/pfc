<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['actualizarEstudiante'])) {

    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
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
    $idCiclo = (int)($_POST['idCiclo'] ?? 0);
    $cursosPermitidos = ['Grado Medio', 'Grado Superior', '1º', '2º'];
    $curso = in_array($_POST['curso'] ?? '', $cursosPermitidos, true) ? $_POST['curso'] : 'Grado Medio';

    if ($idEstudiante <= 0) {
        header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
        exit;
    }

    $errores = [];
    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    if (empty($email)) {
        $errores['emailEstudiante'] = "El email es obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $errores['emailEstudiante'] = "El formato del email no es válido.";
    }
    if (empty($dni)) $errores['dniEstudiante'] = "El DNI es obligatorio.";
    if (empty($telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono es obligatorio.";
    } elseif (!Security::validatePhone($telefono)) {
        $errores['telefonoEstudiante'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $errores['codigoPostalEstudiante'] = "El código postal debe ser numérico.";
    }
    if ($idCiclo <= 0) $errores['idCiclo'] = "Selecciona el ciclo.";

    if (empty($errores) && checkEstudianteExistente($dni, $email, $idEstudiante)) {
        $errores['dniEstudiante'] = "El DNI o Email ya están registrados por otro estudiante.";
    }

    if (empty($errores)) {
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

    header("Location: ../../../vistas/admin/estudiantes/modificarEstudiantes.php?idEstudiante=" . $idEstudiante);
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>

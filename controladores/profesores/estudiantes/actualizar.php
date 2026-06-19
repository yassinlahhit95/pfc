<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (!isset($_POST['actualizarEstudiante'])) {
    header("Location: ../../../vistas/profesores/estudiantes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);

if (!estudiantePerteneceAProfesor($idEstudiante, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
    header("Location: ../../../vistas/profesores/estudiantes/lista.php"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nombre        = trim($_POST['nombreEstudiante']);
$email         = trim($_POST['emailEstudiante']);
$dni           = trim($_POST['dniEstudiante']);
$telefono      = trim($_POST['telefonoEstudiante']);
$fechaNacimiento = trim($_POST['fechaNacimientoEstudiante']);
$direccion     = trim($_POST['direccionEstudiante']);
$ciudad        = trim($_POST['ciudadEstudiante']);
$codigoPostal  = trim($_POST['codigoPostalEstudiante']);
$observaciones = isset($_POST['observacionesEstudiante']) ? trim($_POST['observacionesEstudiante']) : '';
$idCiclo       = (int)($_POST['idCiclo'] ?? 0);

$errores = [];

if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es obligatorio.";
if (empty($email)) {
    $errores['emailEstudiante'] = "El email es obligatorio.";
} elseif (!Security::validateEmail($email)) {
    $errores['emailEstudiante'] = "El formato del email no es válido.";
}
if (empty($dni)) $errores['dniEstudiante'] = "El DNI es obligatorio.";
if (!empty($telefono) && !Security::validatePhone($telefono)) {
    $errores['telefonoEstudiante'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";
}
if (empty($idCiclo)) $errores['idCiclo'] = "Debe seleccionar un ciclo.";

if (empty($errores) && checkEstudianteExistente($dni, $email, $idEstudiante)) {
    $errores['dniEstudiante'] = "El DNI o Email ya están registrados.";
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (empty($errores)) {
    $estudianteOriginal = obtenerEstudiantePorId($idEstudiante);
    $fechaAlta          = $estudianteOriginal['fechaAltaEstudiante'];

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

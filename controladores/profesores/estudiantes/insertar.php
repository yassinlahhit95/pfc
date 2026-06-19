<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (!isset($_POST['guardarEstudiante'])) {
    header("Location: ../../../vistas/profesores/estudiantes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nombre         = trim($_POST['nombreEstudiante']);
$email          = trim($_POST['emailEstudiante']);
$dni            = trim($_POST['dniEstudiante']);
$telefono       = trim($_POST['telefonoEstudiante']);
$fechaNacimiento= trim($_POST['fechaNacimientoEstudiante']);
$fechaAlta      = date('Y-m-d');
$direccion      = trim($_POST['direccionEstudiante']);
$ciudad         = trim($_POST['ciudadEstudiante']);
$codigoPostal   = trim($_POST['codigoPostalEstudiante']);
$observaciones  = isset($_POST['observacionesEstudiante']) ? trim($_POST['observacionesEstudiante']) : '';
$idCiclo        = (int)($_POST['idCiclo'] ?? 0);
$cursosPermitidos = ['Grado Medio', 'Grado Superior', '1º', '2º'];
$curso          = in_array($_POST['curso'] ?? '', $cursosPermitidos, true) ? $_POST['curso'] : 'Grado Medio';

$avisos = [];
if (empty($nombre)) $avisos['nombreEstudiante'] = "El nombre es obligatorio.";
if (empty($email)) {
    $avisos['emailEstudiante'] = "Falta el email";
} elseif (!Security::validateEmail($email)) {
    $avisos['emailEstudiante'] = "El formato del email no es válido.";
}
if (empty($dni)) $avisos['dniEstudiante'] = "El DNI es obligatorio.";
if (empty($telefono)) {
    $avisos['telefonoEstudiante'] = "El teléfono es obligatorio.";
} elseif (!Security::validatePhone($telefono)) {
    $avisos['telefonoEstudiante'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";
}
if (empty($fechaNacimiento)) $avisos['fechaNacimientoEstudiante'] = "La fecha de nacimiento es obligatoria.";
if ($idCiclo <= 0) $avisos['idCiclo'] = "Selecciona un ciclo";

if (empty($avisos) && checkEstudianteExistente($dni, $email)) {
    $avisos['dniEstudiante'] = "El DNI o Email ya están registrados.";
}

if (!empty($avisos)) {
    $_SESSION['errores'] = $avisos;
    $_SESSION['datos_estudiante'] = $_POST;
    header("Location: ../../../vistas/profesores/estudiantes/agregar.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso)) {
    $_SESSION['exito'] = mensajeExitoConCredenciales("Estudiante registrado correctamente.");
    header("Location: ../../../vistas/profesores/estudiantes/lista.php");
    exit;
}
$_SESSION['errores'] = "Hubo un problema al intentar guardar el estudiante.";
header("Location: ../../../vistas/profesores/estudiantes/agregar.php");
exit;

<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php"); exit;
}

$idEstudiante    = (int)($_POST['idEstudiante'] ?? 0);
$nombre          = Security::sanitize($_POST['nombre'] ?? '');
$email           = strtolower(trim($_POST['email'] ?? ''));
$idCiclo         = (int)($_POST['idCiclo'] ?? 0);
$dni             = Security::sanitize($_POST['dni'] ?? '');
$telefono        = Security::sanitize($_POST['telefono'] ?? '');
$direccion       = Security::sanitize($_POST['direccion'] ?? '');
$ciudad          = Security::sanitize($_POST['ciudad'] ?? '');
$codigoPostal    = Security::sanitize($_POST['codigoPostal'] ?? '');
$observaciones   = Security::sanitize($_POST['observaciones'] ?? '');
$fechaNacimiento = Security::sanitize($_POST['fechaNacimiento'] ?? '');
$fechaAlta       = Security::sanitize($_POST['fechaAltaEstudiante'] ?? '');
$curso           = Security::sanitize($_POST['curso'] ?? 'Grado Medio');
$anioEstudio     = Security::sanitize($_POST['anioEstudio'] ?? '');

$errores = [];
if ($idEstudiante <= 0) $errores['general'] = "Estudiante no válido.";
if (empty($nombre))     $errores['nombre']  = "El nombre es obligatorio.";
if (empty($email) || !Security::validateEmail($email)) $errores['email'] = "Email no válido.";
if ($idCiclo <= 0)      $errores['idCiclo'] = "Debes seleccionar un ciclo.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_estudiante'] = $_POST;
    header("Location: ../../../vistas/secretaria/estudiantes/modificarEstudiantes.php?id=$idEstudiante");
    exit;
}

$ok = actualizarEstudiante(
    $idEstudiante,
    $nombre,
    $email,
    $telefono,
    $fechaNacimiento,
    $dni,
    $fechaAlta,
    $direccion,
    $ciudad,
    $codigoPostal,
    $observaciones,
    $idCiclo,
    $curso,
    $anioEstudio ?: null
);

if ($ok) {
    registrarAccionSecretaria('actualizar', 'estudiantes', $idEstudiante, $nombre);
    $_SESSION['exito'] = "Estudiante actualizado correctamente.";
    header("Location: ../../../vistas/secretaria/estudiantes/verDetallesEstudiantes.php?id=$idEstudiante");
} else {
    $_SESSION['errores'] = ["general" => "Error al actualizar el estudiante."];
    header("Location: ../../../vistas/secretaria/estudiantes/modificarEstudiantes.php?id=$idEstudiante");
}
exit;

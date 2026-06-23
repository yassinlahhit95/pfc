<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
    exit;
}

$idEstudiante    = (int)($_POST['idEstudiante'] ?? 0);
$nombre          = Security::sanitize($_POST['nombre'] ?? '');
$email           = strtolower(trim($_POST['email'] ?? ''));
$idCiclo         = (int)($_POST['idCiclo'] ?? 0);
$dni             = Security::sanitize($_POST['dni'] ?? '');
$telefono        = Security::sanitize($_POST['telefono'] ?? '');
$direccion       = Security::sanitize($_POST['direccion'] ?? '');
$fechaNacimiento = Security::sanitize($_POST['fechaNacimiento'] ?? '');

$errores = [];
if ($idEstudiante <= 0) $errores[] = "Estudiante no válido.";
if (empty($nombre))     $errores[] = "El nombre es obligatorio.";
if (empty($email) || !Security::validateEmail($email)) $errores[] = "Email no válido.";
if ($idCiclo <= 0)      $errores[] = "Debes seleccionar un ciclo.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/estudiantes/modificarEstudiantes.php?id=$idEstudiante");
    exit;
}

$ok = actualizarEstudiante($idEstudiante, $nombre, $email, $idCiclo, $dni, $telefono, $direccion, $fechaNacimiento);

if ($ok) {
    $_SESSION['exito'] = "Estudiante actualizado correctamente.";
} else {
    $_SESSION['errores'] = "Error al actualizar el estudiante.";
}
header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
exit;

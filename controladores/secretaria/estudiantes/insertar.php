<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/estudiantes/agregarEstudiantes.php");
    exit;
}

$nombre         = Security::sanitize($_POST['nombre'] ?? '');
$email          = strtolower(trim($_POST['email'] ?? ''));
$password       = $_POST['password'] ?? '';
$idCiclo        = (int)($_POST['idCiclo'] ?? 0);
$dni            = Security::sanitize($_POST['dni'] ?? '');
$telefono       = Security::sanitize($_POST['telefono'] ?? '');
$direccion      = Security::sanitize($_POST['direccion'] ?? '');
$fechaNacimiento = Security::sanitize($_POST['fechaNacimiento'] ?? '');

$errores = [];
if (empty($nombre))   $errores[] = "El nombre es obligatorio.";
if (empty($email) || !Security::validateEmail($email)) $errores[] = "Email no válido.";
if (empty($password) || strlen($password) < 6) $errores[] = "La contraseña debe tener al menos 6 caracteres.";
if ($idCiclo <= 0)    $errores[] = "Debes seleccionar un ciclo.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/estudiantes/agregarEstudiantes.php");
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
$ok   = insertarEstudiante($nombre, $email, $hash, $idCiclo, $dni, $telefono, $direccion, $fechaNacimiento);

if ($ok) {
    $_SESSION['exito'] = "Estudiante añadido correctamente.";
} else {
    $_SESSION['errores'] = "Error al guardar el estudiante. El email puede estar en uso.";
}
header("Location: ../../../vistas/secretaria/estudiantes/verEstudiantes.php");
exit;

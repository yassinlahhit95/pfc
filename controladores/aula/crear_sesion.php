<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/profesores/aula/crear.php");
    exit;
}

Security::validateCSRFToken($_POST['csrf_token'] ?? '') or die('CSRF validation failed');

$idModulo = (int)($_POST['idModulo'] ?? 0);
$titulo = Security::sanitize($_POST['titulo'] ?? '');
$descripcion = Security::sanitize($_POST['descripcion'] ?? '');
$fechaSesion = $_POST['fechaSesion'] ?? '';
$horaSesion = $_POST['horaSesion'] ?? '';
$enlaceReunion = $_POST['enlaceReunion'] ?? '';
$plataforma = Security::sanitize($_POST['plataforma'] ?? '');

$errores = [];

if (empty($titulo)) $errores[] = 'El título es requerido';
if (empty($fechaSesion)) $errores[] = 'La fecha es requerida';
if (empty($horaSesion)) $errores[] = 'La hora es requerida';
if (empty($enlaceReunion)) $errores[] = 'El enlace es requerido';
if (empty($plataforma)) $errores[] = 'La plataforma es requerida';

$validacionFecha = validarFechaHoraSesion($fechaSesion, $horaSesion);
if ($validacionFecha) $errores[] = $validacionFecha;

$validacionURL = validarEnlaceReunion($enlaceReunion);
if ($validacionURL) $errores[] = $validacionURL;

if (!empty($errores)) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header("Location: ../../vistas/profesores/aula/crear.php");
    Logger::warning('Validación fallida en crear_sesion', ['profesor' => $idProfesor, 'errores' => $errores]);
    exit;
}

$idSesion = crearSesionViva($idModulo, $idProfesor, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);

if ($idSesion) {
    $_SESSION['exito'] = 'Sesión viva creada exitosamente';
    Logger::activity('SESION_CREADA', $idProfesor, ['idSesion' => $idSesion, 'titulo' => $titulo]);

    notificarEstudiantesPorModulo($idModulo, 'NUEVA_SESION', 'Nueva Sesión Viva', "Se ha programado una nueva sesión viva: $titulo el $fechaSesion a las $horaSesion", $idSesion, 'SESION');

    header("Location: ../../vistas/profesores/aula/sesiones.php");
} else {
    $_SESSION['errores'] = 'Error al crear la sesión. Intenta de nuevo.';
    Logger::error('Error creando sesión', ['profesor' => $idProfesor, 'titulo' => $titulo]);
    header("Location: ../../vistas/profesores/aula/crear.php");
}
?>

<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../../include/Logger.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN Y VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$idProfesor = $_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/profesores/aula/crear.php");
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = "Solicitud inválida (error de seguridad). Por favor, intenta de nuevo.";
    header("Location: ../../vistas/profesores/aula/crear.php");
    exit;
}

$idModulo = (int)($_POST['idModulo'] ?? 0);
$titulo = Security::sanitize($_POST['titulo'] ?? '');
$descripcion = Security::sanitize($_POST['descripcion'] ?? '');
$fechaSesion = $_POST['fechaSesion'] ?? '';
$horaSesion = $_POST['horaSesion'] ?? '';
$enlaceReunion = $_POST['enlaceReunion'] ?? '';
$plataforma = Security::sanitize($_POST['plataforma'] ?? '');

$errores = [];
if (empty($titulo)) $errores[] = 'El título es obligatorio.';
if (empty($fechaSesion)) $errores[] = 'La fecha es obligatoria.';
if (empty($horaSesion)) $errores[] = 'La hora es obligatoria.';
if (empty($enlaceReunion)) $errores[] = 'El enlace de reunión es obligatorio.';
if (empty($plataforma)) $errores[] = 'La plataforma es obligatoria.';

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

if (!$idModulo || !in_array($idProfesor, listarProfesoresDeModulo($idModulo))) {
    $_SESSION['errores'] = "No tienes permiso para acceder a este módulo.";
    header("Location: ../../vistas/profesores/aula/crear.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO Y RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$idSesion = crearSesionViva($idModulo, $idProfesor, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);

if ($idSesion) {
    $_SESSION['exito'] = 'Sesión viva creada exitosamente';
    Logger::activity('SESION_CREADA', $idProfesor, ['idSesion' => $idSesion, 'titulo' => $titulo]);
    notificarEstudiantesPorModulo($idModulo, 'NUEVA_SESION', 'Nueva Sesión Viva', "Se ha programado una nueva sesión viva: $titulo el $fechaSesion a las $horaSesion", $idSesion, 'SESION');
    header("Location: ../../vistas/profesores/aula/sesiones.php");
} else {
    $_SESSION['errores'] = 'Error al crear la sesión. Inténtalo de nuevo.';
    Logger::error('Error creando sesión', ['profesor' => $idProfesor, 'titulo' => $titulo]);
    header("Location: ../../vistas/profesores/aula/crear.php");
}
?>

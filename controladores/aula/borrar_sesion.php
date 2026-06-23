<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Logger.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idProfesor = $_SESSION['idProfesor'];
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/profesores/aula/sesiones.php"); exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}
$idSesion = (int)($_POST['id'] ?? 0);

$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (borrarSesionViva($idSesion)) {
    $_SESSION['exito'] = 'La sesión ha sido eliminada correctamente.';
    Logger::activity('SESION_ELIMINADA', $idProfesor, ['idSesion' => $idSesion, 'titulo' => $sesion['titulo']]);
} else {
    $_SESSION['errores'] = 'Error al eliminar la sesión. Inténtalo de nuevo.';
    Logger::error('Error eliminando sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
}

header("Location: ../../vistas/profesores/aula/sesiones.php");

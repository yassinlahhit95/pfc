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
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Método no permitido']); exit; }
    header("Location: ../../vistas/profesores/aula/sesiones.php"); exit;
}

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Solicitud inválida']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}
$idSesion = (int)($_POST['id'] ?? 0);

$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No encontrado o sin permiso']); exit; }
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (borrarSesionViva($idSesion)) {
    $_SESSION['exito'] = 'La sesión ha sido eliminada correctamente.';
    Logger::activity('SESION_ELIMINADA', $idProfesor, ['idSesion' => $idSesion, 'titulo' => $sesion['titulo']]);
    if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>'Sesión eliminada']); exit; }
} else {
    $_SESSION['errores'] = 'Error al eliminar la sesión. Inténtalo de nuevo.';
    Logger::error('Error eliminando sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Error al eliminar']); exit; }
}

header("Location: ../../vistas/profesores/aula/sesiones.php");

<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
$idProfesor = $_SESSION['idProfesor'];

require_once __DIR__ . "/../../../modelos/aula.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!Security::validateCSRFToken(null, false)) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Solicitud inválida']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idSesion = $_GET['id'] ?? $_POST['idSesion'] ?? 0;

if (!$idSesion) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Sesión no especificada']); exit; }
    $_SESSION['errores'] = "No se ha especificado la sesión a eliminar.";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$sesion = obtenerSesionPorId($idSesion);
if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'No tienes permiso']); exit; }
    $_SESSION['errores'] = "No tienes permiso para eliminar esta sesión.";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$idModulo = $sesion['idModulo'];
$ok       = borrarSesionViva($idSesion);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($ok) {
    if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>'Sesión eliminada correctamente']); exit; }
    $_SESSION['exito'] = "La sesión ha sido eliminada correctamente.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
} else {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Error al eliminar la sesión']); exit; }
    $_SESSION['errores'] = "Error al eliminar la sesión. Inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
}
exit;


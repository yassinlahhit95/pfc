<?php
/**
 * Registrar evento de analytics
 * Recibe JSON POST con datos del evento
 */

session_start();
require_once __DIR__ . "/../../modelos/aula.php";

// Solo registrar si hay sesión activa
if (empty($_SESSION['idEstudiante']) && empty($_SESSION['idProfesor'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['accion'])) {
    http_response_code(400);
    exit;
}

$idUsuario = $data['idUsuario'] ?? ($_SESSION['idEstudiante'] ?? $_SESSION['idProfesor'] ?? 0);
$tipoUsuario = $data['tipoUsuario'] ?? ($_SESSION['idEstudiante'] ? 'estudiante' : 'profesor');
$accion = trim($data['accion']);
$idModulo = intval($data['idModulo'] ?? 0);
$metadatos = $data['metadatos'] ?? null;

// Validar acción (prevenir inyección)
$accionesPermitidas = ['descargar', 'subir', 'ver', 'entrega', 'busqueda', 'paginacion', 'tab_switch', 'modal_open', 'tema_change', 'session_end'];
if (!in_array($accion, $accionesPermitidas)) {
    http_response_code(400);
    exit;
}

// Registrar en BD
$ok = registrarAnalytics($idUsuario, $tipoUsuario, $accion, $idModulo, $metadatos);

http_response_code($ok ? 200 : 500);
echo json_encode(['ok' => $ok]);
exit;
?>

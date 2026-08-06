<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../include/Security.php";
Security::initSession();
require_once __DIR__ . "/../../modelos/aula.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (empty($_SESSION['idEstudiante']) && empty($_SESSION['idProfesor'])) {
    http_response_code(401);
    exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(200);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['accion'])) {
    http_response_code(400);
    exit;
}

$idUsuario   = $_SESSION['idEstudiante'] ?? $_SESSION['idProfesor'] ?? 0;
$tipoUsuario = !empty($_SESSION['idEstudiante']) ? 'estudiante' : 'profesor';
$accion      = trim($data['accion']);
$idModulo    = intval($data['idModulo'] ?? 0);
$metadatos   = $data['metadatos'] ?? null;

// Lista blanca de acciones permitidas (previene inyección de datos arbitrarios)
$accionesPermitidas = ['descargar', 'subir', 'ver', 'entrega', 'busqueda', 'paginacion', 'tab_switch', 'modal_open', 'tema_change', 'session_end'];
if (!in_array($accion, $accionesPermitidas)) {
    http_response_code(400);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$ok = registrarAnalytics($idUsuario, $tipoUsuario, $accion, $idModulo, $metadatos);

http_response_code($ok ? 200 : 500);
echo json_encode(['ok' => $ok]);
exit;

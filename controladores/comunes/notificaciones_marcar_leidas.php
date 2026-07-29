<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
// POST /controladores/comunes/notificaciones_marcar_leidas.php — { ids: [1,2,3] }
// Marca como leídas notificaciones genéricas (tabla `notificaciones`,
// modelos/notificaciones.php) del usuario de la sesión actual. Llamado desde
// dashboard-shell.js cuando se abre la campana de notificaciones — el panel
// ya muestra el mensaje completo, así que verlo ahí cuenta como "visto".
// No toca mensajería/chat (tienen su propio flujo de lectura al navegar al
// hilo), solo las filas de esta tabla genérica.
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/notificaciones.php';
require_once __DIR__ . '/../../modelos/aula.php';

$roles = [
    'admin'      => $_SESSION['idAdmin']      ?? null,
    'profesor'   => $_SESSION['idProfesor']   ?? null,
    'secretaria' => $_SESSION['idSecretaria'] ?? null,
    'estudiante' => $_SESSION['idEstudiante'] ?? null,
    'tutor'      => $_SESSION['idTutor']      ?? null,
];
$tipoUsuario = null;
$idUsuario   = null;
foreach ($roles as $tipo => $id) {
    if (!empty($id)) { $tipoUsuario = $tipo; $idUsuario = (int)$id; break; }
}
if (!$tipoUsuario) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Sesión no válida.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken($_POST['csrf_token'] ?? null, false)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido.']);
    exit;
}

$ids = $_POST['ids'] ?? [];
if (!is_array($ids)) $ids = [];
$aulaIds = $_POST['aula_ids'] ?? [];
if (!is_array($aulaIds)) $aulaIds = [];

$ok = marcarNotificacionesLeidas($idUsuario, $tipoUsuario, $ids)
    && marcarNotificacionesAulaLeidas($idUsuario, $tipoUsuario, $aulaIds);

echo json_encode(['ok' => $ok]);

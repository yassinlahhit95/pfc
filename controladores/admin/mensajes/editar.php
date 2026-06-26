<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
if (!FeatureGuard::check('feature_mensajes')) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Módulo desactivado']);
    exit;
}
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/log.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Token inválido o expirado']);
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);
$contenido     = trim($_POST['contenido'] ?? '');

if ($idReclamacion <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Identificador no válido']);
    exit;
}
if ($contenido === '') {
    echo json_encode(['ok' => false, 'msg' => 'El mensaje no puede estar vacío']);
    exit;
}
if (mb_strlen($contenido) > 1000) {
    echo json_encode(['ok' => false, 'msg' => 'Máximo 1000 caracteres']);
    exit;
}

$msg = obtenerMensajePorId($idReclamacion);
if (!$msg || $msg['emisor_rol'] !== 'admin') {
    echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para editar este mensaje']);
    exit;
}

if (editarMensaje($idReclamacion, $contenido)) {
    registrarAccion('editar', 'reclamaciones', $idReclamacion);
    echo json_encode(['ok' => true, 'msg' => 'Mensaje editado correctamente']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al guardar el cambio']);
}
exit;

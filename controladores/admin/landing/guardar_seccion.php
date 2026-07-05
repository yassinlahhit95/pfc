<?php
// Guarda el contenido de una sección del borrador (AJAX JSON).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../include/landing/secciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

$idSeccion = (int)($_POST['idSeccion'] ?? 0);
$seccion   = $idSeccion > 0 ? obtenerSeccionPorId($idSeccion) : null;
if (!$seccion) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Sección no encontrada.']);
    exit;
}

$datos = json_decode($_POST['contenido'] ?? '', true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($datos)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Contenido inválido.']);
    exit;
}

$limpio = landing_sanear_contenido($seccion['tipo'], $datos);
if (is_string($limpio)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => $limpio]);
    exit;
}

$ok = actualizarContenidoSeccion($idSeccion, json_encode($limpio, JSON_UNESCAPED_UNICODE));
if ($ok) {
    registrarAccion('actualizar', 'landing', $idSeccion, 'Sección «' . $seccion['tipo'] . '» actualizada');
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => 'Sección guardada.']
    : ['ok' => false, 'msg' => 'No se pudo guardar la sección.']);

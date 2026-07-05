<?php
// Persiste el nuevo orden de las secciones del borrador (AJAX JSON).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

$ids = json_decode($_POST['orden'] ?? '', true);
if (!is_array($ids) || !$ids) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Orden inválido.']);
    exit;
}
$ids = array_map('intval', $ids);
if (in_array(0, $ids, true)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Orden inválido.']);
    exit;
}

$ok = actualizarOrdenSecciones($ids);
if ($ok) {
    registrarAccion('actualizar', 'landing', null, 'Secciones reordenadas');
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => 'Orden guardado.']
    : ['ok' => false, 'msg' => 'No se pudo guardar el orden.']);

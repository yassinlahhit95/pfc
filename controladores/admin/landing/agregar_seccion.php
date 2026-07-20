<?php
// Añade una sección nueva al borrador con su contenido por defecto (AJAX JSON).
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

if (!Security::validateCSRFToken(null, false)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
    exit;
}

$tipo  = $_POST['tipo'] ?? '';
$tipos = landing_tipos();
if (!isset($tipos[$tipo])) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Tipo de sección no reconocido.']);
    exit;
}

$idSeccion = insertarSeccionLanding($tipo, json_encode($tipos[$tipo]['defecto'], JSON_UNESCAPED_UNICODE));
if ($idSeccion > 0) {
    registrarAccion('insertar', 'landing', $idSeccion, 'Sección «' . $tipo . '» añadida');
}
ob_clean();
echo json_encode($idSeccion > 0
    ? ['ok' => true, 'msg' => 'Sección añadida.', 'idSeccion' => $idSeccion,
       'tipo' => $tipo, 'nombre' => $tipos[$tipo]['nombre'], 'icono' => $tipos[$tipo]['icono'],
       'contenido' => $tipos[$tipo]['defecto']]
    : ['ok' => false, 'msg' => 'No se pudo añadir la sección.']);

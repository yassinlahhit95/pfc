<?php
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../include/Security.php';
// GET /controladores/comunes/eventos/obtener.php?id=N
// Detalle de un evento (para el modal de edición del calendario). Accesible
// a los 5 roles con sesión iniciada — el propio dato ya está filtrado por
// obtenerEventoPorId (activo=1); no hay guard de rol único porque estudiante/
// tutor/profesor también consultan el calendario, no solo admin/secretaría.
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$sesionValida = !empty($_SESSION['idAdmin']) || !empty($_SESSION['idProfesor'])
    || !empty($_SESSION['idSecretaria']) || !empty($_SESSION['idEstudiante'])
    || !empty($_SESSION['idTutor']);
if (!$sesionValida) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
    exit;
}

require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_eventos');

require_once __DIR__ . "/../../../modelos/eventos.php";

$idEvento = (int)($_GET['id'] ?? 0);
$evento   = $idEvento > 0 ? obtenerEventoPorId($idEvento) : false;

if (!$evento) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'msg' => 'Evento no encontrado.']);
    exit;
}

$recordatorios = $evento['recordatorios'] ?? [];
unset($evento['recordatorios']);

echo json_encode(['ok' => true, 'evento' => $evento, 'recordatorios' => $recordatorios]);

<?php
// GET /controladores/comunes/eventos/listar.php?start=YYYY-MM-DD&end=YYYY-MM-DD
// Eventos del calendario para el usuario de la sesión actual, en el rango de
// fechas dado. Admin ve todos los eventos (gestión); el resto de roles ve
// solo lo que le corresponde según tipo_visibilidad/audiencia_json del evento.
require_once __DIR__ . '/../../../include/Security.php';
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_eventos');

require_once __DIR__ . "/../../../modelos/eventos.php";

$start = trim($_GET['start'] ?? '') ?: null;
$end   = trim($_GET['end'] ?? '') ?: null;

[$idUsuario, $tipoUsuario] = eventosUsuarioSesion();

try {
    if ($tipoUsuario === 'director') {
        $eventos = listarEventosDirector($start, $end);
    } elseif (!$tipoUsuario) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
        exit;
    } else {
        $eventos = obtenerEventosParaUsuario($idUsuario, $tipoUsuario, $start, $end);
    }
    echo json_encode(['ok' => true, 'eventos' => $eventos]);
} catch (Throwable $e) {
    error_log("eventos/listar.php error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'msg' => 'Error al obtener eventos']);
    exit;
}

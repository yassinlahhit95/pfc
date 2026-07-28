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

if (!empty($_SESSION['idAdmin'])) {
    $eventos = listarTodosEventos(['solo_activos' => true]);
    if ($start !== null) $eventos = array_values(array_filter($eventos, fn($e) => $e['fechaEvento'] >= $start));
    if ($end   !== null) $eventos = array_values(array_filter($eventos, fn($e) => $e['fechaEvento'] <= $end));
} else {
    $roles = [
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
        echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
        exit;
    }
    $eventos = obtenerEventosParaUsuario($idUsuario, $tipoUsuario, $start, $end);
}

echo json_encode(['ok' => true, 'eventos' => $eventos]);

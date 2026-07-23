<?php
// Reinicia el tour de bienvenida de un usuario (AJAX, solo admin) — pensado
// para usarse con data-ajax-confirm (public/js/core/modal-confirm.js).
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tours.php";
require_once __DIR__ . "/../../../modelos/log.php";

header('Content-Type: application/json; charset=utf-8');

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida. Inténtelo de nuevo.']);
    exit;
}

$idUsuario   = (int)($_POST['idUsuario'] ?? 0);
$tipoUsuario = $_POST['tipoUsuario'] ?? '';

if (!$idUsuario || !in_array($tipoUsuario, TOUR_ROLES_VALIDOS, true)) {
    echo json_encode(['ok' => false, 'msg' => 'Datos no válidos.']);
    exit;
}

if (reiniciarTourUsuario($idUsuario, $tipoUsuario)) {
    registrarAccion('reiniciar_tour', $tipoUsuario, $idUsuario, "Tour de bienvenida reiniciado para $tipoUsuario #$idUsuario");
    echo json_encode(['ok' => true, 'msg' => 'El tour de bienvenida se mostrará de nuevo en su próximo inicio de sesión.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo reiniciar el tour.']);
}

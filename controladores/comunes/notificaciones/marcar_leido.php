<?php
// POST /controladores/comunes/notificaciones/marcar_leido.php — { idNotificacion }
// Marca como leído un recordatorio de evento (notificaciones_recordatorios) del
// usuario de la sesión actual.
require_once __DIR__ . '/../../../include/Security.php';
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . "/../../../modelos/notificacionesRecordatorios.php";

$roles = [
    'director'   => $_SESSION['idAdmin']      ?? null,
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken(null, false)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido.']);
    exit;
}

$idNotificacion = (int)($_POST['idNotificacion'] ?? 0);
if ($idNotificacion <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'ID no especificado.']);
    exit;
}

// Comprobación de propiedad antes de marcar leído — evita que un usuario
// marque como leída una notificación ajena solo adivinando su id (IDOR).
$con  = obtenerConexion();
$stmt = mysqli_prepare($con, "SELECT idNotificacionRecordatorio FROM notificaciones_recordatorios WHERE idNotificacionRecordatorio = ? AND idUsuario = ? AND tipoUsuario = ?");
mysqli_stmt_bind_param($stmt, "iis", $idNotificacion, $idUsuario, $tipoUsuario);
mysqli_stmt_execute($stmt);
$existe = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if (!$existe) {
    http_response_code(404);
    echo json_encode(['ok' => false, 'msg' => 'Notificación no encontrada.']);
    exit;
}

$ok = marcarComoLeida($idNotificacion);
echo json_encode(['ok' => $ok, 'msg' => $ok ? 'Notificación marcada como leída.' : 'No se pudo marcar la notificación.']);

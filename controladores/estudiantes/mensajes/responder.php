<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../controladores/firebase/firebase_helper.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function responder_est_salir($ok, $msg, $idReclamacion, $isAjax, $extra = []) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra));
        exit;
    }
    if ($ok) { $_SESSION['exito'] = $msg; } else { $_SESSION['errores'] = $msg; }
    header("Location: ../../../vistas/estudiantes/mensajes/detalles.php?id=$idReclamacion");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['enviarRespuesta'])) {
    header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    responder_est_salir(false, "Solicitud no válida o expirada. Recarga la página e inténtalo de nuevo.", (int)($_POST['idReclamacion'] ?? 0), $isAjax);
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);
$respuesta     = trim($_POST['respuesta'] ?? '');

if ($idReclamacion <= 0 || $respuesta === '') {
    responder_est_salir(false, "El contenido de la respuesta no puede estar vacío.", $idReclamacion, $isAjax);
}

$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || (int)$mensaje['idEstudiante'] !== (int)$_SESSION['idEstudiante']) {
    if ($isAjax) { http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'No tienes permiso para responder a este mensaje.']); exit; }
    $_SESSION['errores'] = "No tienes permiso para responder a este mensaje.";
    header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (insertarRespuestaMensaje($idReclamacion, (int)$_SESSION['idEstudiante'], null, $respuesta, 'estudiante')) {
    // Notificar al destinatario original vía FCM
    if ($mensaje['emisor_rol'] === 'profesor' && !empty($mensaje['idProfesor'])) {
        $token = obtenerTokenUsuario($mensaje['idProfesor'], 'profesor');
        if ($token) {
            enviarNotificacionFirebase($token, "Respuesta a tu mensaje", $mensaje['asunto']);
        }
    } elseif ($mensaje['emisor_rol'] === 'admin') {
        $con  = obtenerConexion();
        $dirs = mysqli_query($con, "SELECT fcm_token FROM directores WHERE fcm_token IS NOT NULL AND fcm_token != ''");
        while ($d = mysqli_fetch_assoc($dirs)) {
            enviarNotificacionFirebase($d['fcm_token'], "Respuesta de estudiante", $mensaje['asunto']);
        }
    }

    responder_est_salir(true, "La respuesta ha sido enviada correctamente.", $idReclamacion, $isAjax, [
        'respuesta' => Security::escapeHtml($respuesta),
    ]);
} else {
    responder_est_salir(false, "No se pudo enviar la respuesta. Inténtalo de nuevo.", $idReclamacion, $isAjax);
}

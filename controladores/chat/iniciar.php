<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/chat.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Security::initSession() (not a bare session_start()) so the cookie-hardening
// flags (Secure/HttpOnly/SameSite/strict_mode) actually get applied.
Security::initSession();

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!empty($_SESSION['idAdmin'])) {
    $myRol = 'admin';
    $myId  = (int)$_SESSION['idAdmin'];
    $back  = '../../vistas/admin/chat/index.php';
} elseif (!empty($_SESSION['idProfesor'])) {
    $myRol = 'profesor';
    $myId  = (int)$_SESSION['idProfesor'];
    $back  = '../../vistas/profesores/chat/index.php';
} elseif (!empty($_SESSION['idTutor'])) {
    $myRol = 'tutor';
    $myId  = (int)$_SESSION['idTutor'];
    $back  = '../../vistas/tutores/mensajes/chat.php';
} elseif (!empty($_SESSION['idEstudiante'])) {
    $myRol = 'estudiante';
    $myId  = (int)$_SESSION['idEstudiante'];
    $back  = '../../vistas/estudiantes/chat/index.php';
} elseif (!empty($_SESSION['idSecretaria'])) {
    $myRol = 'secretaria';
    $myId  = (int)$_SESSION['idSecretaria'];
    $back  = '../../vistas/secretaria/mensajes/chat.php';
} else {
    header('Location: ../../vistas/login.php');
    exit;
}

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    header("Location: $back");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $back");
    exit;
}

// rotate=false: el widget de chat flotante hace varias llamadas AJAX seguidas
// (iniciar, enviar, marcar leído...) sobre el mismo token embebido en la
// página sin recargarla — el rotate=true por defecto invalidaría ese token
// justo después de la primera conversación iniciada, dejando todo lo que
// viene después (enviar el primer mensaje, iniciar otra conversación) roto
// hasta el siguiente recargue de página. Mismo patrón que enviar.php.
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '', false)) {
    header("Location: $back");
    exit;
}

$targetRol = trim($_POST['target_rol'] ?? '');
$targetId  = (int)($_POST['target_id'] ?? 0);

$validRoles = ['admin', 'profesor', 'estudiante', 'tutor', 'secretaria'];
if (!in_array($targetRol, $validRoles, true) || $targetId <= 0) {
    header("Location: $back");
    exit;
}

if ($targetRol === $myRol && $targetId === $myId) {
    header("Location: $back");
    exit;
}

// La política de contactos (quién puede hablar con quién) se aplica también
// en el servidor, no solo en el selector de contactos de la interfaz.
if (!chatParEsPermitido($myRol, $myId, $targetRol, $targetId)) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
           && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'msg' => 'No puedes iniciar una conversación con este usuario.']);
        exit;
    }
    header("Location: $back");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$convId = chatEncontrarOCrear($myRol, $myId, $targetRol, $targetId);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
       && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true, 'conv_id' => $convId]);
    exit;
}
$convUrl = (strpos($back, 'index.php') !== false)
            ? str_replace('index.php', 'conversacion.php', $back)
            : str_replace('chat.php', 'conversacion.php', $back);
header("Location: {$convUrl}?id=$convId");
exit;

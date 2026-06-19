<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../controladores/firebase/firebase_helper.php";

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST['enviarRespuesta'])) {
    header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../../vistas/estudiantes/mensajes/lista.php");
    exit;
}

$idReclamacion = (int)($_POST['idReclamacion'] ?? 0);
$respuesta     = trim($_POST['respuesta'] ?? '');

if ($idReclamacion <= 0 || $respuesta === '') {
    $_SESSION['errores'] = "El contenido de la respuesta no puede estar vacío.";
    header("Location: ../../../vistas/estudiantes/mensajes/detalles.php?id=$idReclamacion");
    exit;
}

$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || (int)$mensaje['idEstudiante'] !== (int)$_SESSION['idEstudiante']) {
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

    $_SESSION['exito'] = "La respuesta ha sido enviada correctamente.";
} else {
    $_SESSION['errores'] = "No se pudo enviar la respuesta. Inténtalo de nuevo.";
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/estudiantes/mensajes/detalles.php?id=$idReclamacion");
exit;

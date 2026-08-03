<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function actualizar_msg_salir($ok, $msg, $idReclamacion, $isAjax, $extra = []) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra));
        exit;
    }
    if ($ok) { $_SESSION['exito'] = $msg; } else { $_SESSION['errores'] = $msg; }
    header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
    exit;
}

if (!isset($_POST['idReclamacion'])) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken(null, false)) {
    actualizar_msg_salir(false, "Solicitud inválida. Inténtelo de nuevo.", (int)$_POST['idReclamacion'], $isAjax);
}

$idReclamacion = (int)$_POST['idReclamacion'];

if ($idReclamacion <= 0) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

if (!mensajePerteneceAProfesor($idReclamacion, $_SESSION['idProfesor'])) {
    if ($isAjax) { http_response_code(403); echo json_encode(['ok' => false, 'msg' => 'No tienes permiso sobre este mensaje.']); exit; }
    $_SESSION['errores'] = "No tienes permiso sobre este mensaje.";
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['guardarRespuesta'])) {
    $respuesta = trim($_POST['respuesta'] ?? '');
    if ($respuesta === '') {
        actualizar_msg_salir(false, "El mensaje no puede estar vacío.", $idReclamacion, $isAjax);
    } elseif (insertarRespuestaMensaje($idReclamacion, null, (int)$_SESSION['idProfesor'], $respuesta, 'profesor')) {
        actualizar_msg_salir(true, "Respuesta enviada correctamente.", $idReclamacion, $isAjax, ['respuesta' => Security::escapeHtml($respuesta)]);
    } else {
        actualizar_msg_salir(false, "Error al enviar la respuesta.", $idReclamacion, $isAjax);
    }
} elseif (isset($_POST['marcarLeido'])) {
    if (marcarMensajeComoLeido($idReclamacion)) {
        actualizar_msg_salir(true, "Mensaje marcado como leído.", $idReclamacion, $isAjax);
    } else {
        actualizar_msg_salir(false, "No se pudo actualizar el mensaje.", $idReclamacion, $isAjax);
    }
}

header("Location: ../../../vistas/profesores/mensajes/detalles.php?id=" . $idReclamacion);
exit;

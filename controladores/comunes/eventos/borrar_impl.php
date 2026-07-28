<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/eventos/borrar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'ID no especificado';

if (!Security::validateCSRFToken(null, false)) {
    $msg = 'Solicitud inválida.';
} else {
    $idEvento = (int)($_POST['idEvento'] ?? 0);
    $evento   = $idEvento > 0 ? obtenerEventoPorId($idEvento) : false;

    if (!$evento) {
        $msg = 'El evento no existe o ya ha sido eliminado.';
    // Ver editar_impl.php: idCreador no distingue secretarias individuales
    // (instalación single-tenant), así que admin y secretaría borran por igual.
    } elseif ($rolBase !== 'admin' && $rolBase !== 'secretaria') {
        $msg = 'No tienes permiso para eliminar este evento.';
    } elseif (borrarEventoSuave($idEvento)) {
        if ($rolBase === 'admin') {
            registrarAccion('borrar', 'eventos', $idEvento, $evento['tituloEvento']);
        } else {
            registrarAccionSecretaria('borrar', 'eventos', $idEvento, $evento['tituloEvento']);
        }
        $ok = true;
        $msg = 'Evento eliminado.';
    } else {
        $msg = 'No se pudo eliminar el evento.';
    }
}

if ($ok) $_SESSION['exito'] = $msg; else $_SESSION['errores'] = $msg;

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/$rolBase/eventos/gestionEventos.php");
exit;

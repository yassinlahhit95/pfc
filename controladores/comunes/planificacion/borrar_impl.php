<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/planificacion/borrar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// Llamado siempre vía el modal data-modal-borrar (modal-borrar.js) — el
// checklist no es una tabla, así que la fila no se puede desvanecer sola;
// el enlace lleva data-redirect para recargar la página tras eliminar.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/planificacion.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'Solicitud inválida.';

// El Guard ya validó el CSRF de esta petición con rotate=false — igual aquí.
if (!Security::validateCSRFToken(null, false)) {
    $msg = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
} else {
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        $msg = 'Tarea no especificada.';
    } elseif (borrarPlanTarea($id)) {
        if ($rolBase === 'admin') {
            registrarAccion('borrar', 'planificacion', $id, '');
        } else {
            registrarAccionSecretaria('borrar', 'planificacion', $id, '');
        }
        $ok = true;
        $msg = 'Tarea eliminada.';
    } else {
        $msg = 'No se pudo eliminar la tarea.';
    }
}

if ($ok) $_SESSION['exito'] = $msg; else $_SESSION['errores'] = $msg;

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/$rolBase/planificacion/planificacion.php");
exit;

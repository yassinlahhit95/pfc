<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/planificacion/toggle.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// Soporta AJAX (widget del dashboard) y POST clásico (página completa) —
// mismo patrón $isAjax que borrar_impl.php.
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
    $completada = isset($_POST['completada']) && $_POST['completada'] === '1';

    if ($id <= 0) {
        $msg = 'Tarea no especificada.';
    } else {
        $tipoCompletadaPor = null;
        $nombreCompletadaPor = null;
        if ($completada) {
            if ($rolBase === 'admin') {
                require_once __DIR__ . "/../../../modelos/directores.php";
                $tipoCompletadaPor = 'director';
                $nombreCompletadaPor = obtenerDirectorPorId((int)($_SESSION['idAdmin'] ?? 0))['nombreDirector'] ?? 'Director/a';
            } else {
                require_once __DIR__ . "/../../../modelos/secretarias.php";
                $tipoCompletadaPor = 'secretaria';
                $nombreCompletadaPor = obtenerSecretariaPorId((int)($_SESSION['idSecretaria'] ?? 0))['nombreSecretaria'] ?? 'Secretaría';
            }
        }

        if (togglePlanTarea($id, $completada, $tipoCompletadaPor, $nombreCompletadaPor)) {
            if ($rolBase === 'admin') {
                registrarAccion('toggle', 'planificacion', $id, $completada ? 'completada' : 'pendiente');
            } else {
                registrarAccionSecretaria('toggle', 'planificacion', $id, $completada ? 'completada' : 'pendiente');
            }
            $ok = true;
            $msg = 'Actualizado.';
        } else {
            $msg = 'No se pudo actualizar la tarea.';
        }
    }
}

if ($ok) $_SESSION['exito'] = $msg; else $_SESSION['errores'] = $msg;

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode([
        'ok' => $ok, 'msg' => $msg, 'completadaPorNombre' => $nombreCompletadaPor ?? null,
        // Belt-and-suspenders: see insertar_impl.php's comment on this field.
        'new_csrf' => $ok ? null : Security::generateCSRFToken(),
    ]);
    exit;
}
header("Location: ../../../vistas/$rolBase/planificacion/planificacion.php");
exit;

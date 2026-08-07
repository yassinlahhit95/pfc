<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/planificacion/insertar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/planificacion.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'Solicitud inválida.'; $idPlanTarea = null;

// El Guard ya validó el CSRF de esta petición con rotate=false; re-validar
// aquí con el rotating default lo borraría innecesariamente — usar false
// también, igual que blog/ofertaCiclos/eventos (ver CLAUDE.md).
if (!Security::validateCSRFToken(null, false)) {
    $msg = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
} else {
    $texto = trim($_POST['texto'] ?? '');

    if ($texto === '') {
        $msg = 'Escribe qué hay que hacer.';
    } elseif (mb_strlen($texto) > 500) {
        $msg = 'El texto es demasiado largo (máx. 500 caracteres).';
    } else {
        $tipoCreador = $rolBase === 'admin' ? 'director' : 'secretaria';
        $idCreador = $rolBase === 'admin'
            ? (int)($_SESSION['idAdmin'] ?? 0)
            : (int)($_SESSION['idSecretaria'] ?? 0);

        $id = insertarPlanTarea($texto, $tipoCreador, $idCreador);
        if ($id === false) {
            $msg = 'No se pudo guardar la tarea.';
        } else {
            if ($rolBase === 'admin') {
                registrarAccion('insertar', 'planificacion', $id, $texto);
            } else {
                registrarAccionSecretaria('insertar', 'planificacion', $id, $texto);
            }
            $ok = true;
            $msg = 'Tarea añadida.';
            $idPlanTarea = $id;
        }
    }
}

if ($ok) $_SESSION['exito'] = $msg; else $_SESSION['errores'] = $msg;

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode([
        'ok' => $ok, 'msg' => $msg, 'idPlanTarea' => $idPlanTarea, 'texto' => $texto ?? '',
        // Belt-and-suspenders: AdminGuard/SecretariaGuard already catch a bad
        // token before this file even runs and return their own new_csrf, but
        // keep this in sync in case that check is ever loosened for this route.
        'new_csrf' => $ok ? null : Security::generateCSRFToken(),
    ]);
    exit;
}
header("Location: ../../../vistas/$rolBase/planificacion/planificacion.php");
exit;

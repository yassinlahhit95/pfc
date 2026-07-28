<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/eventos/editar.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'Solicitud inválida.';

if (!Security::validateCSRFToken(null, false)) {
    $msg = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
} else {
    $idEvento = (int)($_POST['idEvento'] ?? 0);
    $evento   = $idEvento > 0 ? obtenerEventoPorId($idEvento) : false;

    if (!$evento) {
        $msg = 'El evento no existe o ya ha sido eliminado.';
    // eventos.idCreador solo referencia directores.idDirector (instalación
    // single-tenant, ver crear_impl.php) — no distingue qué secretaria en
    // concreto creó el evento, así que admin y secretaría gestionan por
    // igual cualquier evento; solo se bloquean roles ajenos a ambos guards.
    } elseif ($rolBase !== 'admin' && $rolBase !== 'secretaria') {
        $msg = 'No tienes permiso para editar este evento.';
    } else {
        $titulo = trim($_POST['tituloEvento'] ?? '');
        $fecha  = trim($_POST['fechaEvento'] ?? '');
        if ($titulo === '' || $fecha === '') {
            $msg = 'El título y la fecha del evento son obligatorios.';
        } else {
            $recordatorios = $_POST['recordatorios'] ?? null;

            $data = [
                'tituloEvento'      => $titulo,
                'descripcionEvento' => trim($_POST['descripcionEvento'] ?? ''),
                'fechaEvento'       => $fecha,
                'horaEvento'        => (($_POST['horaEvento'] ?? '') !== '') ? $_POST['horaEvento'] : null,
                'ubicacionEvento'   => trim($_POST['ubicacionEvento'] ?? ''),
                'tipo_visibilidad'  => $_POST['tipo_visibilidad'] ?? ($evento['tipo_visibilidad'] ?? 'publica'),
                'audiencia_json'    => $_POST['audiencia_json'] ?? null,
            ];
            if (is_array($recordatorios)) {
                $data['recordatorios'] = $recordatorios;
            }

            if (editarEvento($idEvento, $data)) {
                if ($rolBase === 'admin') {
                    registrarAccion('actualizar', 'eventos', $idEvento, $titulo);
                } else {
                    registrarAccionSecretaria('actualizar', 'eventos', $idEvento, $titulo);
                }
                $ok = true;
                $msg = 'El evento ha sido actualizado correctamente.';
            } else {
                $msg = 'Ocurrió un error al intentar actualizar el evento.';
            }
        }
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

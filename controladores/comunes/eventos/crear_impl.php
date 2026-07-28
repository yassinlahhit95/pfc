<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/eventos/crear.php
// El wrapper de cada rol ya validó el Guard correspondiente y debe definir
// $rolBase ('admin' | 'secretaria') antes de hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$ok = false; $msg = 'Solicitud inválida.'; $idEvento = null;

// El modal del calendario reenvía este mismo formulario varias veces sin
// recargar la página (crear varios eventos seguidos) — rotate=false para que
// el token siga siendo válido en la siguiente llamada (mismo motivo que el
// resto de controladores AJAX de un modal persistente, ver CLAUDE.md).
if (!Security::validateCSRFToken(null, false)) {
    $msg = 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.';
} else {
    $titulo = trim($_POST['tituloEvento'] ?? '');
    $fecha  = trim($_POST['fechaEvento'] ?? '');

    if ($titulo === '' || $fecha === '') {
        $msg = 'El título y la fecha del evento son obligatorios.';
    } else {
        // eventos.idCreador solo puede referenciar directores.idDirector
        // (fk_eventos_creador) — instalación single-tenant con un único
        // director, así que también se usa para atribuir los eventos
        // creados desde secretaría (esa tabla no tiene FK propia aquí).
        if ($rolBase === 'admin') {
            $idCreador = (int)($_SESSION['idAdmin'] ?? 0);
        } else {
            $con = obtenerConexion();
            $filaDirector = mysqli_fetch_assoc(mysqli_query($con, "SELECT idDirector FROM directores ORDER BY idDirector ASC LIMIT 1"));
            $idCreador = (int)($filaDirector['idDirector'] ?? 0);
        }

        $recordatorios = $_POST['recordatorios'] ?? [];
        if (!is_array($recordatorios)) $recordatorios = [];

        $data = [
            'tituloEvento'      => $titulo,
            'descripcionEvento' => trim($_POST['descripcionEvento'] ?? ''),
            'fechaEvento'       => $fecha,
            'horaEvento'        => (($_POST['horaEvento'] ?? '') !== '') ? $_POST['horaEvento'] : null,
            'ubicacionEvento'   => trim($_POST['ubicacionEvento'] ?? ''),
            'idCreador'         => $idCreador,
            'tipo_visibilidad'  => $_POST['tipo_visibilidad'] ?? 'publica',
            'audiencia_json'    => $_POST['audiencia_json'] ?? null,
            'recordatorios'     => $recordatorios,
        ];

        $idEvento = crearEvento($data);
        if ($idEvento === false) {
            $msg = 'Ocurrió un error al intentar crear el evento.';
            $idEvento = null;
        } else {
            $idEvento = (int)$idEvento;
            if ($rolBase === 'admin') {
                registrarAccion('insertar', 'eventos', $idEvento, $titulo);
            } else {
                registrarAccionSecretaria('insertar', 'eventos', $idEvento, $titulo);
            }
            $ok = true;
            $msg = 'El evento ha sido creado correctamente.';
        }
    }
}

if ($ok) $_SESSION['exito'] = $msg; else $_SESSION['errores'] = $msg;

if ($isAjax) {
    header('Content-Type: application/json');
    unset($_SESSION['exito'], $_SESSION['errores']);
    $payload = ['ok' => $ok, 'msg' => $msg];
    if ($ok) $payload['idEvento'] = $idEvento;
    echo json_encode($payload);
    exit;
}
header("Location: ../../../vistas/$rolBase/eventos/gestionEventos.php");
exit;

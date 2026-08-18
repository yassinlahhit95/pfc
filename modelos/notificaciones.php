<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/../include/Cache.php";

// ══════════════════════════════════════════════════════════════════════
// Notificaciones genéricas de navbar — para eventos que no tienen ya su
// propio "leido"/estado natural (mensajería, chat, justificaciones...):
// p.ej. que un director asigne un nuevo ciclo/módulo a un profesor. Cada
// fila es una notificación puntual con su propio flag `leido`, a diferencia
// de los contadores de "cola pendiente" (admisiones, justificaciones) que
// bajan solos a 0 cuando se resuelve el elemento subyacente.
// ══════════════════════════════════════════════════════════════════════

function crearNotificacion(int $idUsuario, string $tipoUsuario, string $tipo, string $mensaje, ?string $url = null): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO notificaciones (idUsuario, tipoUsuario, tipo, mensaje, url) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "issss", $idUsuario, $tipoUsuario, $tipo, $mensaje, $url);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok) Cache::forget("notif_no_leidas_{$tipoUsuario}_{$idUsuario}");
    return $ok;
}

function contarNotificacionesNoLeidas(int $idUsuario, string $tipoUsuario): int {
    return Cache::remember("notif_no_leidas_{$tipoUsuario}_{$idUsuario}", 10, function () use ($idUsuario, $tipoUsuario) {
        $con  = obtenerConexion();
        $stmt = mysqli_prepare($con,
            "SELECT COUNT(*) AS n FROM notificaciones WHERE idUsuario = ? AND tipoUsuario = ? AND leido = 0");
        mysqli_stmt_bind_param($stmt, "is", $idUsuario, $tipoUsuario);
        mysqli_stmt_execute($stmt);
        return (int)(mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['n'] ?? 0);
    });
}

// Solo las no leídas — igual que $mensajesNotifProf en profesores/comunes/nav.php,
// esto alimenta la vista previa de la campana, no un historial completo.
function listarNotificacionesNoLeidas(int $idUsuario, string $tipoUsuario, int $limite = 5): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM notificaciones WHERE idUsuario = ? AND tipoUsuario = ? AND leido = 0
         ORDER BY idNotificacion DESC LIMIT ?");
    mysqli_stmt_bind_param($stmt, "isi", $idUsuario, $tipoUsuario, $limite);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

// Marca como leídas únicamente las notificaciones que pertenecen a este
// usuario/rol (nunca confiar en IDs sueltos del cliente sin este filtro).
function marcarNotificacionesLeidas(int $idUsuario, string $tipoUsuario, array $ids): bool {
    $ids = array_values(array_unique(array_map('intval', $ids)));
    $ids = array_filter($ids, fn($id) => $id > 0);
    if (empty($ids)) return true;

    $con         = obtenerConexion();
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $tipos        = str_repeat('i', count($ids));
    $stmt = mysqli_prepare($con,
        "UPDATE notificaciones SET leido = 1
         WHERE idUsuario = ? AND tipoUsuario = ? AND idNotificacion IN ($placeholders)");
    mysqli_stmt_bind_param($stmt, "is{$tipos}", $idUsuario, $tipoUsuario, ...$ids);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok) Cache::forget("notif_no_leidas_{$tipoUsuario}_{$idUsuario}");
    return $ok;
}

function marcarTodasNotificacionesLeidas(int $idUsuario, string $tipoUsuario): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE notificaciones SET leido = 1 WHERE idUsuario = ? AND tipoUsuario = ? AND leido = 0");
    mysqli_stmt_bind_param($stmt, "is", $idUsuario, $tipoUsuario);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok) Cache::forget("notif_no_leidas_{$tipoUsuario}_{$idUsuario}");
    return $ok;
}

// Historial completo (leídas + no leídas) para una bandeja de entrada real —
// a diferencia de listarNotificacionesNoLeidas(), que solo alimenta la
// vista previa de la campana con las pendientes.
function listarNotificaciones(int $idUsuario, string $tipoUsuario, int $limite = 50): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM notificaciones WHERE idUsuario = ? AND tipoUsuario = ?
         ORDER BY idNotificacion DESC LIMIT ?");
    mysqli_stmt_bind_param($stmt, "isi", $idUsuario, $tipoUsuario, $limite);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// Configuración de recordatorios (cuándo debe dispararse un aviso respecto
// a un evento). Los recordatorios en sí NO envían notificaciones —
// eso lo hace el sistema de notificaciones (fuera de este archivo).
// ══════════════════════════════════════════════════════════════════════

// Tipos de recordatorio soportados y sus minutos de antelación.
const RECORDATORIOS_TIPOS_DEFECTO = [
    '24h_antes' => 1440,
    '1h_antes'  => 60,
    'en_inicio' => 0,
];

// Crea los 3 recordatorios por defecto de un evento (24h antes, 1h antes, al inicio).
// Se invoca automáticamente desde crearEvento(). ON DUPLICATE KEY UPDATE evita
// duplicados si ya existían (uk_evento_tipo en idEvento + tipo_recordatorio).
function crearRecordatoriosDefecto(int $idEvento): bool {
    $idEvento = (int)$idEvento;
    $con = obtenerConexion();
    $sql = "INSERT INTO recordatorios (idEvento, tipo_recordatorio, minutos_antes)
            VALUES (?, ?, ?), (?, ?, ?), (?, ?, ?)
            ON DUPLICATE KEY UPDATE minutos_antes = VALUES(minutos_antes)";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar crearRecordatoriosDefecto: " . mysqli_error($con));
        return false;
    }
    $tipo1 = '24h_antes'; $min1 = 1440;
    $tipo2 = '1h_antes';  $min2 = 60;
    $tipo3 = 'en_inicio'; $min3 = 0;
    mysqli_stmt_bind_param(
        $stmt,
        "isiisiisi",
        $idEvento, $tipo1, $min1,
        $idEvento, $tipo2, $min2,
        $idEvento, $tipo3, $min3
    );
    $ok = mysqli_stmt_execute($stmt);
    if (!$ok) {
        error_log("Error al ejecutar crearRecordatoriosDefecto (idEvento={$idEvento}): " . mysqli_stmt_error($stmt));
    }
    mysqli_stmt_close($stmt);
    return $ok;
}

// Devuelve todos los recordatorios de un evento, del de mayor antelación al menor.
function obtenerRecordatorios(int $idEvento): array {
    $idEvento = (int)$idEvento;
    $con = obtenerConexion();
    $sql = "SELECT idRecordatorio, idEvento, tipo_recordatorio, minutos_antes, activo
            FROM recordatorios WHERE idEvento = ? ORDER BY minutos_antes DESC";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Error al preparar obtenerRecordatorios: " . mysqli_error($con));
        return [];
    }
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    return $lista;
}

// Sincroniza qué recordatorios están activos para un evento: desactiva todos
// y vuelve a activar solo los tipos seleccionados. $tiposSeleccionados es un
// array de valores del enum ('24h_antes', '1h_antes', 'en_inicio').
function sincronizarRecordatorios(int $idEvento, array $tiposSeleccionados): bool {
    $idEvento = (int)$idEvento;
    $con = obtenerConexion();

    // 1. Desactivar todos los recordatorios del evento.
    $sqlDesactivar = "UPDATE recordatorios SET activo = 0 WHERE idEvento = ?";
    $stmtDesactivar = mysqli_prepare($con, $sqlDesactivar);
    if (!$stmtDesactivar) {
        error_log("Error al preparar sincronizarRecordatorios (desactivar): " . mysqli_error($con));
        return false;
    }
    mysqli_stmt_bind_param($stmtDesactivar, "i", $idEvento);
    $ok = mysqli_stmt_execute($stmtDesactivar);
    if (!$ok) {
        error_log("Error al desactivar recordatorios (idEvento={$idEvento}): " . mysqli_stmt_error($stmtDesactivar));
    }
    mysqli_stmt_close($stmtDesactivar);
    if (!$ok || empty($tiposSeleccionados)) {
        return $ok;
    }

    // 2. Reactivar únicamente los tipos seleccionados, filtrando contra el
    // enum válido para no colar valores arbitrarios en el IN (...).
    $tipos = array_values(array_intersect($tiposSeleccionados, array_keys(RECORDATORIOS_TIPOS_DEFECTO)));
    if (empty($tipos)) {
        return true;
    }
    $placeholders = implode(',', array_fill(0, count($tipos), '?'));
    $sqlActivar = "UPDATE recordatorios SET activo = 1 WHERE idEvento = ? AND tipo_recordatorio IN ($placeholders)";
    $stmtActivar = mysqli_prepare($con, $sqlActivar);
    if (!$stmtActivar) {
        error_log("Error al preparar sincronizarRecordatorios (activar): " . mysqli_error($con));
        return false;
    }
    $tipoStr = str_repeat('s', count($tipos));
    mysqli_stmt_bind_param($stmtActivar, "i{$tipoStr}", $idEvento, ...$tipos);
    $ok = mysqli_stmt_execute($stmtActivar);
    if (!$ok) {
        error_log("Error al activar recordatorios (idEvento={$idEvento}): " . mysqli_stmt_error($stmtActivar));
    }
    mysqli_stmt_close($stmtActivar);
    return $ok;
}

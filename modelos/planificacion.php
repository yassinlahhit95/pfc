<?php
// Cuaderno de planificación compartido entre director y secretaría — un único
// listado de tareas por centro (no hay noción de "tablero personal").

// Prepared statement (not mysqli_query) so numeric/tinyint columns come back
// as native PHP int/bool via mysqlnd's binary protocol instead of strings —
// mysqli_query's text protocol returns every column as a string, which made
// json_encode emit "completada":"1" instead of 1. The mobile app's
// `json['completada'] == 1` check never matched that string, so a task
// toggled complete always displayed as still pending after the list refresh.
function listarPlanificacion(): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM planificacion_tareas ORDER BY completada ASC, fechaCreacion ASC");
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $items = [];
    while ($fila = mysqli_fetch_assoc($res)) $items[] = $fila;
    return $items;
}

// Pendientes más antiguas primero, limitadas — para el widget compacto del dashboard.
function listarPlanificacionPendientes(int $limite = 5): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM planificacion_tareas WHERE completada = 0 ORDER BY fechaCreacion ASC LIMIT ?");
    mysqli_stmt_bind_param($stmt, 'i', $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $items = [];
    while ($fila = mysqli_fetch_assoc($res)) $items[] = $fila;
    return $items;
}

function contarPlanificacionPendientes(): int {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT COUNT(*) AS c FROM planificacion_tareas WHERE completada = 0");
    return (int)(mysqli_fetch_assoc($res)['c'] ?? 0);
}

function insertarPlanTarea(string $texto, string $tipoCreador, int $idCreador): int|false {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO planificacion_tareas (texto, tipoCreador, idCreador) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssi', $texto, $tipoCreador, $idCreador);
    if (!mysqli_stmt_execute($stmt)) return false;
    return mysqli_insert_id($con);
}

// $tipoCompletadaPor/$nombreCompletadaPor solo se usan al completar (marcan quién
// la ha terminado); al reabrir una tarea (completada=false) ambos se limpian, no
// se conserva "quién la reabrió" — el histórico solo importa para completadas.
function togglePlanTarea(int $idPlanTarea, bool $completada, ?string $tipoCompletadaPor = null, ?string $nombreCompletadaPor = null): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE planificacion_tareas SET completada = ?, fechaCompletada = ?, tipoCompletadaPor = ?, completadaPorNombre = ? WHERE idPlanTarea = ?");
    $val = $completada ? 1 : 0;
    $fecha = $completada ? date('Y-m-d H:i:s') : null;
    $tipo = $completada ? $tipoCompletadaPor : null;
    $nombre = $completada ? $nombreCompletadaPor : null;
    mysqli_stmt_bind_param($stmt, 'isssi', $val, $fecha, $tipo, $nombre, $idPlanTarea);
    return mysqli_stmt_execute($stmt);
}

function borrarPlanTarea(int $idPlanTarea): bool {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM planificacion_tareas WHERE idPlanTarea = ?");
    mysqli_stmt_bind_param($stmt, 'i', $idPlanTarea);
    return mysqli_stmt_execute($stmt);
}

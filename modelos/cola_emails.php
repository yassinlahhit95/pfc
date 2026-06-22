<?php
require_once __DIR__ . '/conectar.php';

// ══════════════════════════════════════════════════════════════════════
// COLA DE EMAILS ASÍNCRONA
// ══════════════════════════════════════════════════════════════════════

function encolarEmail(string $to, string $nombre, string $asunto, string $html): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        'INSERT INTO cola_emails (destinatario_email, destinatario_nombre, asunto, html_content)
         VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($stmt, 'ssss', $to, $nombre, $asunto, $html);
    return mysqli_stmt_execute($stmt);
}

function obtenerEmailsPendientes(int $limite = 10): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        'SELECT * FROM cola_emails
         WHERE estado = "pendiente" AND intentos < 3
         ORDER BY creado_at ASC
         LIMIT ?');
    mysqli_stmt_bind_param($stmt, 'i', $limite);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

function marcarEmailEnviado(int $id): void {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        'UPDATE cola_emails SET estado = "enviado", enviado_at = NOW() WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
}

function marcarEmailFallido(int $id, string $error): void {
    $con  = obtenerConexion();
    // After 3 attempts mark permanently failed; otherwise stay pending for retry.
    $stmt = mysqli_prepare($con,
        'UPDATE cola_emails
         SET intentos      = intentos + 1,
             ultimo_error  = ?,
             estado        = IF(intentos + 1 >= 3, "fallido", "pendiente")
         WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $error, $id);
    mysqli_stmt_execute($stmt);
}

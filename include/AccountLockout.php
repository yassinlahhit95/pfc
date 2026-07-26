<?php
// Bloqueo por cuenta (email) para ataques de fuerza bruta distribuidos (botnet).
// Complementa el rate-limit por IP de validacion.php.
class AccountLockout {

    // ══════════════════════════════════════════════════════════════════════
    // CONFIGURACIÓN
    // ══════════════════════════════════════════════════════════════════════

    const MAX_FAILS    = 8;    // fallos permitidos por ventana
    const WINDOW       = 900;  // ventana de conteo: 15 min
    const LOCK_SECONDS = 900;  // duración del bloqueo: 15 min

    // ══════════════════════════════════════════════════════════════════════
    // CONSULTAS
    // ══════════════════════════════════════════════════════════════════════

    // account_lockout ya está garantizada por noDeploy/database.sql — sin
    // CREATE TABLE IF NOT EXISTS en cada request (ver CLAUDE.md: nos costó
    // caro ya una vez con historial_secretarias, no repetir el patrón).

    // Devuelve ['locked' => bool, 'minutes' => int].
    public static function status($con, string $email): array {
        if (!$con) return ['locked' => false, 'minutes' => 0];
        $email = strtolower(trim($email));

        $stmt = mysqli_prepare($con, "SELECT locked_until FROM account_lockout WHERE email = ?");
        if (!$stmt) {
            return ['locked' => false, 'minutes' => 0];
        }
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($row && !empty($row['locked_until']) && (int)$row['locked_until'] > time()) {
            return ['locked' => true, 'minutes' => (int)ceil(((int)$row['locked_until'] - time()) / 60)];
        }
        return ['locked' => false, 'minutes' => 0];
    }

    // ══════════════════════════════════════════════════════════════════════
    // OPERACIONES
    // ══════════════════════════════════════════════════════════════════════

    public static function recordFailure($con, string $email): void {
        if (!$con) return;
        $email = strtolower(trim($email));
        $now   = time();

        mysqli_begin_transaction($con);
        try {
            $stmt = mysqli_prepare($con,
                "SELECT intentos, window_start FROM account_lockout WHERE email = ? FOR UPDATE");
            if (!$stmt) {
                mysqli_rollback($con);
                return;
            }
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$row) {
                $ins = mysqli_prepare($con,
                    "INSERT INTO account_lockout (email, intentos, window_start) VALUES (?, 1, ?)");
                if ($ins) {
                    mysqli_stmt_bind_param($ins, "si", $email, $now);
                    mysqli_stmt_execute($ins);
                }
            } elseif ($now - (int)$row['window_start'] > self::WINDOW) {
                $upd = mysqli_prepare($con,
                    "UPDATE account_lockout SET intentos = 1, window_start = ?, locked_until = NULL WHERE email = ?");
                if ($upd) {
                    mysqli_stmt_bind_param($upd, "is", $now, $email);
                    mysqli_stmt_execute($upd);
                }
            } else {
                $intentos = (int)$row['intentos'] + 1;
                if ($intentos >= self::MAX_FAILS) {
                    $until = $now + self::LOCK_SECONDS;
                    $upd   = mysqli_prepare($con,
                        "UPDATE account_lockout SET intentos = ?, locked_until = ? WHERE email = ?");
                    if ($upd) {
                        mysqli_stmt_bind_param($upd, "iis", $intentos, $until, $email);
                        mysqli_stmt_execute($upd);
                    }
                } else {
                    $upd = mysqli_prepare($con,
                        "UPDATE account_lockout SET intentos = ? WHERE email = ?");
                    if ($upd) {
                        mysqli_stmt_bind_param($upd, "is", $intentos, $email);
                        mysqli_stmt_execute($upd);
                    }
                }
            }
            mysqli_commit($con);
        } catch (\Throwable $e) {
            mysqli_rollback($con);
        }
    }

    public static function clear($con, string $email): void {
        if (!$con) return;
        $email = strtolower(trim($email));
        $del = mysqli_prepare($con, "DELETE FROM account_lockout WHERE email = ?");
        if ($del) {
            mysqli_stmt_bind_param($del, "s", $email);
            @mysqli_stmt_execute($del);
        }
    }
}

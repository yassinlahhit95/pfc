<?php
/**
 * AccountLockout — bloqueo de fuerza bruta POR CUENTA (email), independiente de
 * la IP. Complementa el rate-limit por IP de validacion.php: frena ataques
 * distribuidos (botnet) que prueban contraseñas contra un único usuario.
 *
 * Crea su tabla automáticamente (hosting compartido sin migraciones manuales).
 */
class AccountLockout {

    const MAX_FAILS    = 8;    // fallos permitidos por ventana
    const WINDOW       = 900;  // 15 min de ventana de conteo
    const LOCK_SECONDS = 900;  // 15 min de bloqueo al superar el límite

    private static $ensured = false;

    private static function ensureTable($con): void {
        if (self::$ensured) return;
        @mysqli_query($con, "CREATE TABLE IF NOT EXISTS account_lockout (
            email VARCHAR(190) NOT NULL,
            intentos INT UNSIGNED NOT NULL DEFAULT 0,
            window_start INT UNSIGNED NOT NULL,
            locked_until INT UNSIGNED NULL,
            PRIMARY KEY (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        self::$ensured = true;
    }

    /** ['locked'=>bool, 'minutes'=>int] */
    public static function status($con, string $email): array {
        if (!$con) return ['locked' => false, 'minutes' => 0];
        self::ensureTable($con);
        $email = strtolower(trim($email));

        $stmt = mysqli_prepare($con, "SELECT locked_until FROM account_lockout WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($row && !empty($row['locked_until']) && (int)$row['locked_until'] > time()) {
            return ['locked' => true, 'minutes' => (int)ceil(((int)$row['locked_until'] - time()) / 60)];
        }
        return ['locked' => false, 'minutes' => 0];
    }

    public static function recordFailure($con, string $email): void {
        if (!$con) return;
        self::ensureTable($con);
        $email = strtolower(trim($email));
        $now = time();

        $stmt = mysqli_prepare($con, "SELECT intentos, window_start FROM account_lockout WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$row) {
            $ins = mysqli_prepare($con, "INSERT INTO account_lockout (email, intentos, window_start) VALUES (?, 1, ?)");
            mysqli_stmt_bind_param($ins, "si", $email, $now);
            @mysqli_stmt_execute($ins);
            return;
        }

        if ($now - (int)$row['window_start'] > self::WINDOW) {
            $upd = mysqli_prepare($con, "UPDATE account_lockout SET intentos = 1, window_start = ?, locked_until = NULL WHERE email = ?");
            mysqli_stmt_bind_param($upd, "is", $now, $email);
            @mysqli_stmt_execute($upd);
            return;
        }

        $intentos = (int)$row['intentos'] + 1;
        if ($intentos >= self::MAX_FAILS) {
            $until = $now + self::LOCK_SECONDS;
            $upd = mysqli_prepare($con, "UPDATE account_lockout SET intentos = ?, locked_until = ? WHERE email = ?");
            mysqli_stmt_bind_param($upd, "iis", $intentos, $until, $email);
            @mysqli_stmt_execute($upd);
        } else {
            $upd = mysqli_prepare($con, "UPDATE account_lockout SET intentos = ? WHERE email = ?");
            mysqli_stmt_bind_param($upd, "is", $intentos, $email);
            @mysqli_stmt_execute($upd);
        }
    }

    public static function clear($con, string $email): void {
        if (!$con) return;
        self::ensureTable($con);
        $email = strtolower(trim($email));
        $del = mysqli_prepare($con, "DELETE FROM account_lockout WHERE email = ?");
        mysqli_stmt_bind_param($del, "s", $email);
        @mysqli_stmt_execute($del);
    }
}

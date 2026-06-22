<?php
// Limitación de peticiones por IP para endpoints públicos (sin sesión):
// pre-matrícula, consulta por DNI, formularios de contacto, etc.
// Crea su tabla automáticamente la primera vez.
class RateLimiter {

    // ══════════════════════════════════════════════════════════════════════
    // TABLA
    // ══════════════════════════════════════════════════════════════════════

    private static $ensured = false;

    private static function ensureTable($con): void {
        if (self::$ensured) return;
        @mysqli_query($con, "CREATE TABLE IF NOT EXISTS rate_limits (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            scope VARCHAR(64) NOT NULL,
            ip VARCHAR(45) NOT NULL,
            hits INT UNSIGNED NOT NULL DEFAULT 0,
            window_start INT UNSIGNED NOT NULL,
            blocked_until INT UNSIGNED NULL,
            PRIMARY KEY (id),
            UNIQUE KEY uq_scope_ip (scope, ip)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        self::$ensured = true;
    }

    // ══════════════════════════════════════════════════════════════════════
    // IP DEL CLIENTE
    // ══════════════════════════════════════════════════════════════════════

    private static function clientIp(): string {
        // HTTP_CF_CONNECTING_IP solo es fiable si REMOTE_ADDR pertenece a Cloudflare.
        // Sin esa comprobación, un atacante podría falsificar la cabecera para eludir el rate-limit.
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && self::isCloudflareIp($remote)) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else {
            $ip = $remote;
        }
        return substr($ip, 0, 45);
    }

    // Rangos IPv4 de Cloudflare — actualizar periódicamente desde https://www.cloudflare.com/ips/.
    private static function isCloudflareIp(string $ip): bool {
        $cfRanges = [
            '173.245.48.0/20','103.21.244.0/22','103.22.200.0/22','103.31.4.0/22',
            '141.101.64.0/18','108.162.192.0/18','190.93.240.0/20','188.114.96.0/20',
            '197.234.240.0/22','198.41.128.0/17','162.158.0.0/15','104.16.0.0/13',
            '104.24.0.0/14','172.64.0.0/13','131.0.72.0/22',
        ];
        $ipLong = ip2long($ip);
        if ($ipLong === false) return false;
        foreach ($cfRanges as $range) {
            [$net, $bits] = explode('/', $range);
            $mask = ~((1 << (32 - (int)$bits)) - 1);
            if ((ip2long($net) & $mask) === ($ipLong & $mask)) return true;
        }
        return false;
    }

    // ══════════════════════════════════════════════════════════════════════
    // COMPROBACIÓN
    // ══════════════════════════════════════════════════════════════════════

    // Devuelve true si la petición está permitida; false si se superó el límite.
    public static function allow($con, string $scope, int $maxHits = 20,
                                 int $windowSeconds = 300, int $blockSeconds = 900): bool {
        if (!$con) return true; // fail-open solo si no hay DB (evita romper el sitio)
        self::ensureTable($con);

        $ip  = self::clientIp();
        $now = time();

        // Wrap in a transaction with SELECT FOR UPDATE to prevent concurrent requests
        // from the same IP racing to INSERT the first row or miscounting hits.
        mysqli_begin_transaction($con);
        try {
            $stmt = mysqli_prepare($con, "SELECT hits, window_start, blocked_until FROM rate_limits WHERE scope = ? AND ip = ? FOR UPDATE");
            mysqli_stmt_bind_param($stmt, "ss", $scope, $ip);
            mysqli_stmt_execute($stmt);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if (!$row) {
                $ins = mysqli_prepare($con, "INSERT INTO rate_limits (scope, ip, hits, window_start) VALUES (?, ?, 1, ?)");
                mysqli_stmt_bind_param($ins, "ssi", $scope, $ip, $now);
                mysqli_stmt_execute($ins);
                mysqli_commit($con);
                return true;
            }

            if (!empty($row['blocked_until']) && (int)$row['blocked_until'] > $now) {
                mysqli_commit($con);
                return false;
            }

            if ($now - (int)$row['window_start'] > $windowSeconds) {
                $upd = mysqli_prepare($con, "UPDATE rate_limits SET hits = 1, window_start = ?, blocked_until = NULL WHERE scope = ? AND ip = ?");
                mysqli_stmt_bind_param($upd, "iss", $now, $scope, $ip);
                mysqli_stmt_execute($upd);
                mysqli_commit($con);
                return true;
            }

            $hits = (int)$row['hits'] + 1;
            if ($hits > $maxHits) {
                $until = $now + $blockSeconds;
                $upd = mysqli_prepare($con, "UPDATE rate_limits SET hits = ?, blocked_until = ? WHERE scope = ? AND ip = ?");
                mysqli_stmt_bind_param($upd, "iiss", $hits, $until, $scope, $ip);
                mysqli_stmt_execute($upd);
                mysqli_commit($con);
                return false;
            }

            $upd = mysqli_prepare($con, "UPDATE rate_limits SET hits = ? WHERE scope = ? AND ip = ?");
            mysqli_stmt_bind_param($upd, "iss", $hits, $scope, $ip);
            mysqli_stmt_execute($upd);
            mysqli_commit($con);
            return true;
        } catch (\Throwable $e) {
            mysqli_rollback($con);
            return true; // fail-open: don't block legitimate users on DB error
        }
    }
}

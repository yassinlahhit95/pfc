<?php
// Limitación de peticiones por IP para endpoints públicos (sin sesión):
// pre-matrícula, consulta por DNI, formularios de contacto, etc.
// Crea su tabla automáticamente la primera vez.
class RateLimiter {

    // rate_limits ya está garantizada por noDeploy/database.sql — sin
    // CREATE TABLE IF NOT EXISTS en cada request.


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

    // Rangos IPv4 e IPv6 de Cloudflare — actualizar periódicamente desde https://www.cloudflare.com/ips/.
    private static function isCloudflareIp(string $ip): bool {
        $cfRanges = [
            '173.245.48.0/20','103.21.244.0/22','103.22.200.0/22','103.31.4.0/22',
            '141.101.64.0/18','108.162.192.0/18','190.93.240.0/20','188.114.96.0/20',
            '197.234.240.0/22','198.41.128.0/17','162.158.0.0/15','104.16.0.0/13',
            '104.24.0.0/14','172.64.0.0/13','131.0.72.0/22',
            '2400:cb00::/32','2606:4700::/32','2803:f800::/32','2405:b500::/32',
            '2405:8100::/32','2a06:98c0::/29','2c0f:f248::/32'
        ];

        $ipBin = @inet_pton($ip);
        if ($ipBin === false) return false;
        $ipLen = strlen($ipBin);

        foreach ($cfRanges as $range) {
            $parts = explode('/', $range);
            $net = $parts[0];
            $bits = isset($parts[1]) ? (int)$parts[1] : ($ipLen === 4 ? 32 : 128);
            
            $netBin = @inet_pton($net);
            if ($netBin === false || strlen($netBin) !== $ipLen) continue;

            $mask = str_repeat(chr(255), $bits >> 3);
            $rem = $bits & 7;
            if ($rem > 0) {
                $mask .= chr(255 & (255 << (8 - $rem)));
            }
            $mask = str_pad($mask, $ipLen, chr(0));

            if (($ipBin & $mask) === ($netBin & $mask)) return true;
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


        $ip  = self::clientIp();
        $now = time();

        // Envolver en una transacción con SELECT FOR UPDATE para evitar que peticiones
        // concurrentes de la misma IP compitan por insertar la primera fila o cuenten mal los hits.
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

            // Si el periodo de bloqueo acaba de expirar, o si la ventana de tiempo ha expirado
            // de forma natural, reseteamos los hits a 1 y comenzamos una nueva ventana.
            if (!empty($row['blocked_until']) || ($now - (int)$row['window_start'] > $windowSeconds)) {
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

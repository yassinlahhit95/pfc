<?php
/**
 * LicenseToken — verify HMAC-signed license tokens issued by the SaaS platform.
 *
 * Token format:  base64url(JSON payload) . '.' . base64url(HMAC-SHA256 signature)
 *
 * The signing secret must match the SAAS_LICENSE_SECRET value in both:
 *   - This project's .env  (AulaPro, client-side)
 *   - saas-admin's .env    (SaaS platform, your server)
 *
 * A client who knows the secret would still need to:
 *   1. Write PHP code to forge a token with a future expiry
 *   2. Correctly replicate the payload schema
 *   3. Inject it into the database
 *   4. Keep doing this every 7 days
 * Combined with PHP obfuscation (ionCube) this becomes infeasible.
 */
class LicenseToken
{
    private const GRACE_HOURS = 48; // hours a new install may operate without a token

    // ── Secret loading ────────────────────────────────────────────────────────

    private static ?string $cachedSecret = null;

    private static function secret(): string
    {
        if (self::$cachedSecret !== null) return self::$cachedSecret;

        $path = dirname(__DIR__) . '/.env';
        if (file_exists($path)) {
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if (!$line || $line[0] === '#' || !str_contains($line, '=')) continue;
                [$k, $v] = explode('=', $line, 2);
                if (trim($k) === 'SAAS_LICENSE_SECRET') {
                    self::$cachedSecret = trim($v, " \t\"'");
                    return self::$cachedSecret;
                }
            }
        }

        // Fallback: empty — will cause all tokens to fail verification (secure default)
        self::$cachedSecret = '';
        return '';
    }

    // ── Encoding helpers ──────────────────────────────────────────────────────

    private static function b64Encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64Decode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Verify a token and return its payload array, or null if invalid / expired / tampered.
     *
     * @return array{status:string,features:array<string,bool>,lock:bool,msg:string,msg_type:string,exp:int}|null
     */
    public static function verify(string $token): ?array
    {
        $secret = self::secret();
        if (!$secret) return null;

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) return null;

        [$payloadB64, $sigB64] = $parts;

        // Constant-time comparison prevents timing attacks
        $expected = self::b64Encode(hash_hmac('sha256', $payloadB64, $secret, true));
        if (!hash_equals($expected, $sigB64)) return null;

        $payload = json_decode(self::b64Decode($payloadB64), true);
        if (!is_array($payload)) return null;

        // Reject expired tokens
        if (($payload['exp'] ?? 0) < time()) return null;

        return $payload;
    }

    /**
     * Returns true only when the token is cryptographically valid AND not expired.
     */
    public static function isValid(string $token): bool
    {
        return self::verify($token) !== null;
    }

    /**
     * Determine whether a grace period applies (new install, never synced).
     * Grace: 48h from first DB record creation if saas_last_sync is NULL.
     */
    public static function inGracePeriod(?string $lastSync, ?string $createdAt): bool
    {
        if ($lastSync !== null) return false; // already synced before — no grace
        if ($createdAt === null) return false;

        $created = strtotime($createdAt);
        return $created !== false && (time() - $created) < (self::GRACE_HOURS * 3600);
    }

    /**
     * Generate a token (used by saas-admin only).
     */
    public static function generate(array $payload): string
    {
        $secret = self::secret();
        if (!$secret) throw new \RuntimeException('SAAS_LICENSE_SECRET not configured.');

        $payloadB64 = self::b64Encode(json_encode($payload));
        $sig        = self::b64Encode(hash_hmac('sha256', $payloadB64, $secret, true));
        return $payloadB64 . '.' . $sig;
    }
}

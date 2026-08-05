<?php
// Verificación de tokens de licencia HMAC firmados por la plataforma SaaS.
// Formato del token: base64url(payload JSON) . '.' . base64url(firma HMAC-SHA256)
//
// Firmado con ADMIN_API_SECRET — el mismo secreto que ya autentica las llamadas
// HMAC del panel de control (suspend/activate/heartbeat/etc.) para esta instancia.
// Deliberadamente no existe un secreto de licencia separado ni compartido entre
// clientes: un único valor de confianza para toda la flota significaba que
// regenerarlo (p. ej. tras una fuga sospechada) rompía todas las instancias a
// la vez hasta actualizar el .env de cada una a mano. Con un secreto por
// conexión, rotar la clave de un cliente nunca afecta a los demás.
class LicenseToken
{
    // ══════════════════════════════════════════════════════════════════════
    // CONFIGURACIÓN
    // ══════════════════════════════════════════════════════════════════════

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
                if (trim($k) === 'ADMIN_API_SECRET') {
                    self::$cachedSecret = trim($v, " \t\"'");
                    return self::$cachedSecret;
                }
            }
        }

        // Sin secreto: todos los tokens fallan la verificación (comportamiento seguro por defecto)
        self::$cachedSecret = '';
        return '';
    }

    // ══════════════════════════════════════════════════════════════════════
    // CODIFICACIÓN BASE64 URL-SAFE
    // ══════════════════════════════════════════════════════════════════════

    private static function b64Encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64Decode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    // ══════════════════════════════════════════════════════════════════════
    // API PÚBLICA
    // ══════════════════════════════════════════════════════════════════════

    // Verifica un token y devuelve su payload, o null si es inválido, expirado o manipulado.
    public static function verify(string $token): ?array
    {
        $secret = self::secret();
        if (!$secret) return null;

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) return null;

        [$payloadB64, $sigB64] = $parts;

        // Comparación en tiempo constante (previene timing attacks)
        $expected = self::b64Encode(hash_hmac('sha256', $payloadB64, $secret, true));
        if (!hash_equals($expected, $sigB64)) return null;

        $payload = json_decode(self::b64Decode($payloadB64), true);
        if (!is_array($payload)) return null;

        if (($payload['exp'] ?? 0) < time()) return null;

        return $payload;
    }

    // Devuelve true solo cuando el token es criptográficamente válido y no ha expirado.
    public static function isValid(string $token): bool
    {
        return self::verify($token) !== null;
    }

    // Genera un token (usado únicamente por saas-admin).
    public static function generate(array $payload): string
    {
        $secret = self::secret();
        if (!$secret) throw new \RuntimeException('ADMIN_API_SECRET no está configurado.');

        $payloadB64 = self::b64Encode(json_encode($payload));
        $sig        = self::b64Encode(hash_hmac('sha256', $payloadB64, $secret, true));
        return $payloadB64 . '.' . $sig;
    }
}

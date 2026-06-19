<?php
// TOTP (RFC 6238) autocontenido, sin dependencias externas.
// Compatible con Google Authenticator, Microsoft Authenticator, Authy, etc.
// SHA1 / 6 dígitos / periodo 30 s — estándar de estas aplicaciones.
class Totp {

    // ══════════════════════════════════════════════════════════════════════
    // CONFIGURACIÓN
    // ══════════════════════════════════════════════════════════════════════

    const PERIOD = 30;
    const DIGITS = 6;
    const ALGO   = 'sha1';
    const B32MAP = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    // ══════════════════════════════════════════════════════════════════════
    // GENERACIÓN
    // ══════════════════════════════════════════════════════════════════════

    // Secreto base32 (20 bytes aleatorios → 32 chars).
    public static function generateSecret(int $bytes = 20): string {
        return self::base32Encode(random_bytes($bytes));
    }

    // Código de 6 dígitos para un instante dado (con desplazamiento de pasos).
    public static function codeAt(string $secret, int $timestamp, int $stepOffset = 0): string {
        $key = self::base32Decode($secret);
        $counter = intdiv($timestamp, self::PERIOD) + $stepOffset;
        $bin = pack('J', $counter); // 8 bytes big-endian
        $hash = hash_hmac(self::ALGO, $bin, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $part = ((ord($hash[$offset])   & 0x7F) << 24)
              | ((ord($hash[$offset+1]) & 0xFF) << 16)
              | ((ord($hash[$offset+2]) & 0xFF) << 8)
              |  (ord($hash[$offset+3]) & 0xFF);
        $code = $part % (10 ** self::DIGITS);
        return str_pad((string)$code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    // ══════════════════════════════════════════════════════════════════════
    // VERIFICACIÓN
    // ══════════════════════════════════════════════════════════════════════

    // Verifica un código admitiendo ±$window pasos (desfase de reloj).
    // Recorre todas las ventanas sin short-circuit para no filtrar timing.
    public static function verify(string $secret, string $code, ?int $timestamp = null, int $window = 1): bool {
        $code = preg_replace('/\D/', '', $code);
        if (strlen($code) !== self::DIGITS) return false;
        $timestamp = $timestamp ?? time();
        $ok = false;
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::codeAt($secret, $timestamp, $i), $code)) $ok = true;
        }
        return $ok;
    }

    // URI otpauth:// para generar el QR de alta en la app autenticadora.
    public static function provisioningUri(string $secret, string $label, string $issuer): string {
        return 'otpauth://totp/' . rawurlencode($issuer . ':' . $label)
            . '?secret=' . $secret
            . '&issuer=' . rawurlencode($issuer)
            . '&algorithm=SHA1&digits=' . self::DIGITS . '&period=' . self::PERIOD;
    }

    // ══════════════════════════════════════════════════════════════════════
    // CODIFICACIÓN BASE32
    // ══════════════════════════════════════════════════════════════════════

    public static function base32Encode(string $data): string {
        $out = ''; $buffer = 0; $bitsLeft = 0;
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $buffer = ($buffer << 8) | ord($data[$i]);
            $bitsLeft += 8;
            while ($bitsLeft >= 5) {
                $bitsLeft -= 5;
                $out .= self::B32MAP[($buffer >> $bitsLeft) & 0x1F];
            }
        }
        if ($bitsLeft > 0) {
            $out .= self::B32MAP[($buffer << (5 - $bitsLeft)) & 0x1F];
        }
        return $out;
    }

    public static function base32Decode(string $b32): string {
        $b32 = strtoupper(rtrim($b32, '='));
        $out = ''; $buffer = 0; $bitsLeft = 0;
        $len = strlen($b32);
        for ($i = 0; $i < $len; $i++) {
            $val = strpos(self::B32MAP, $b32[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $out .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $out;
    }
}

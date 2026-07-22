<?php
// ══════════════════════════════════════════════════════════════════════
// CIFRADO DE DATOS PERSONALES (RGPD Art. 32) — AES-256-GCM autenticado.
//
// encrypt()              → aleatorio (IV al azar). Úsalo para todo salvo DNI.
// encryptDeterministic() → mismo texto plano SIEMPRE produce el mismo
//                          cifrado (IV sintético derivado del texto plano
//                          con una subclave separada). Solo para columnas
//                          que necesitan UNIQUE KEY o búsqueda exacta
//                          (DNI) — el resto debe usar encrypt().
// decrypt()              → sirve para ambos (el IV va dentro del propio
//                          valor almacenado). Si el valor no lleva un
//                          prefijo de versión reconocido, se devuelve tal
//                          cual (texto plano heredado, aún no migrado) —
//                          esto permite desplegar este código antes de
//                          ejecutar la migración de datos sin romper nada.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../config/Config.php';

class Crypto {
    private static ?string $encKey = null;
    private static ?string $ivKey  = null;

    private static function claves(): array {
        if (self::$encKey === null) {
            $hex = Config::getInstance()->get('PII_ENCRYPTION_KEY', '');
            if (strlen($hex) !== 64 || !ctype_xdigit($hex)) {
                throw new RuntimeException(
                    'PII_ENCRYPTION_KEY ausente o con formato inválido en .env ' .
                    '(debe ser una cadena hexadecimal de 64 caracteres — 32 bytes).'
                );
            }
            $raw = hex2bin($hex);
            self::$encKey = hash_hkdf('sha256', $raw, 32, 'aulapro-pii-enc-v1');
            self::$ivKey  = hash_hkdf('sha256', $raw, 32, 'aulapro-pii-detiv-v1');
        }
        return [self::$encKey, self::$ivKey];
    }

    public static function encrypt(?string $plaintext): ?string {
        if ($plaintext === null || $plaintext === '') return $plaintext;
        [$encKey] = self::claves();
        return self::sellar('v1:', $plaintext, $encKey, random_bytes(12));
    }

    public static function encryptDeterministic(?string $plaintext): ?string {
        if ($plaintext === null || $plaintext === '') return $plaintext;
        [$encKey, $ivKey] = self::claves();
        $iv = substr(hash_hmac('sha256', $plaintext, $ivKey, true), 0, 12);
        return self::sellar('v1d:', $plaintext, $encKey, $iv);
    }

    private static function sellar(string $prefijo, string $plaintext, string $encKey, string $iv): string {
        $tag = '';
        $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $encKey, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
        if ($ciphertext === false) {
            throw new RuntimeException('Fallo al cifrar el valor.');
        }
        return $prefijo . base64_encode($iv . $tag . $ciphertext);
    }

    public static function isEncrypted(?string $valor): bool {
        return $valor !== null && (str_starts_with($valor, 'v1:') || str_starts_with($valor, 'v1d:'));
    }

    public static function decrypt(?string $valor): ?string {
        if ($valor === null || $valor === '') return $valor;

        $prefijo = str_starts_with($valor, 'v1d:') ? 'v1d:' : (str_starts_with($valor, 'v1:') ? 'v1:' : null);
        if ($prefijo === null) return $valor; // texto plano heredado, aún no migrado

        [$encKey] = self::claves();
        $raw = base64_decode(substr($valor, strlen($prefijo)), true);
        if ($raw === false || strlen($raw) < 28) return null; // corrupto

        $iv  = substr($raw, 0, 12);
        $tag = substr($raw, 12, 16);
        $ciphertext = substr($raw, 28);

        $plano = openssl_decrypt($ciphertext, 'aes-256-gcm', $encKey, OPENSSL_RAW_DATA, $iv, $tag);
        return $plano === false ? null : $plano;
    }
}

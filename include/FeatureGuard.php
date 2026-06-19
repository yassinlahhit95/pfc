<?php
require_once __DIR__ . '/LicenseToken.php';

// Aplicación server-side de feature flags respaldada por un token de licencia firmado.
// Jerarquía de confianza (alta → baja):
//   1. Token válido y no expirado  ← emitido y firmado por el SaaS, no se puede falsificar
//   2. Periodo de gracia (sin token y nunca sincronizado) ← instalación nueva
//   3. Token expirado o manipulado ← FAIL CLOSED (tratado como suspendido)
class FeatureGuard
{
    private const TTL          = 300; // segundos de caché en sesión
    private const SESSION_TS   = '_fg_ts';
    private const SESSION_DATA = '_fg_data';

    // ══════════════════════════════════════════════════════════════════════
    // CARGA Y CACHÉ
    // ══════════════════════════════════════════════════════════════════════

    private static function load(): array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (
            isset($_SESSION[self::SESSION_TS], $_SESSION[self::SESSION_DATA]) &&
            $_SESSION[self::SESSION_TS] > time() - self::TTL
        ) {
            return $_SESSION[self::SESSION_DATA];
        }

        require_once __DIR__ . '/../modelos/conectar.php';
        $con = obtenerConexion();

        // SELECT * — nunca falla aunque falten columnas opcionales (license_token, etc.)
        $res = mysqli_query($con, 'SELECT * FROM configuracion_centro WHERE idConfig = 1 LIMIT 1');
        $row = ($res ? mysqli_fetch_assoc($res) : null) ?? [];

        $data = self::resolve($row);

        $_SESSION[self::SESSION_TS]   = time();
        $_SESSION[self::SESSION_DATA] = $data;

        return $data;
    }

    // ══════════════════════════════════════════════════════════════════════
    // RESOLUCIÓN DEL ESTADO
    // ══════════════════════════════════════════════════════════════════════

    // Prioridad:
    //   A) Token válido firmado  → confiar exclusivamente en el token
    //   B) Periodo de gracia    → confiar en los valores brutos de la BD
    //   C) Sin token / expirado → FAIL CLOSED
    private static function resolve(array $row): array
    {
        $rawToken = $row['license_token'] ?? null;

        // Camino A: token válido firmado
        if ($rawToken) {
            $payload = LicenseToken::verify($rawToken);
            if ($payload !== null) {
                return [
                    'instance_status'      => $payload['status']   ?? 'active',
                    'suspension_message'   => $payload['susp_msg'] ?? '',
                    'feature_prematricula' => (int)(bool)($payload['features']['feature_prematricula'] ?? true),
                    'feature_chat'         => (int)(bool)($payload['features']['feature_chat']         ?? true),
                    'feature_inventario'   => (int)(bool)($payload['features']['feature_inventario']   ?? true),
                    'feature_subida_tfg'   => (int)(bool)($payload['features']['feature_subida_tfg']   ?? true),
                    'saas_lock_features'   => (int)(bool)($payload['lock']     ?? false),
                    'saas_message'         => $payload['msg']      ?? '',
                    'saas_message_type'    => $payload['msg_type'] ?? 'info',
                    '_source'              => 'token',
                ];
            }

            // Token presente pero inválido/expirado → fail closed inmediato (sin gracia)
            return self::suspendedState('Su licencia ha expirado. Contacte con el proveedor para renovarla.');
        }

        // Camino B: sin token — periodo de gracia si NUNCA se licenció
        // license_token_exp solo lo escribe storeLicenseToken (ruta de heartbeat).
        // Si es null → instalación nueva o pre-heartbeat → permitir con valores brutos de BD.
        // Si está definido → un heartbeat corrió antes pero el token ya no está → fail closed.
        $hasEverBeenLicensed = !empty($row['license_token_exp'] ?? null);

        if ($hasEverBeenLicensed) {
            return self::suspendedState('Renovación de licencia requerida. Contacte con el proveedor.');
        }

        // Instalación nueva (columna inexistente aún) o nunca sincronizada → valores brutos de BD
        return [
            'instance_status'      => $row['instance_status']     ?? 'active',
            'suspension_message'   => $row['suspension_message']  ?? '',
            'feature_prematricula' => (int)($row['feature_prematricula'] ?? 1),
            'feature_chat'         => (int)($row['feature_chat']         ?? 1),
            'feature_inventario'   => (int)($row['feature_inventario']   ?? 1),
            'feature_subida_tfg'   => (int)($row['feature_subida_tfg']   ?? 1),
            'saas_lock_features'   => (int)($row['saas_lock_features']   ?? 0),
            'saas_message'         => $row['saas_message']      ?? '',
            'saas_message_type'    => $row['saas_message_type'] ?? 'info',
            '_source'              => 'grace',
        ];
    }

    private static function suspendedState(string $message): array
    {
        return [
            'instance_status'      => 'suspended',
            'suspension_message'   => $message,
            'feature_prematricula' => 0,
            'feature_chat'         => 0,
            'feature_inventario'   => 0,
            'feature_subida_tfg'   => 0,
            'saas_lock_features'   => 1,
            'saas_message'         => $message,
            'saas_message_type'    => 'error',
            '_source'              => 'fail_closed',
        ];
    }

    // ══════════════════════════════════════════════════════════════════════
    // API PÚBLICA
    // ══════════════════════════════════════════════════════════════════════

    public static function check(string $feature): bool
    {
        $data = self::load();
        if (($data['instance_status'] ?? 'active') !== 'active') return false;
        return (bool)($data[$feature] ?? false);
    }

    public static function isLocked(): bool
    {
        return (bool)(self::load()['saas_lock_features'] ?? false);
    }

    public static function getMessage(): string
    {
        return (string)(self::load()['saas_message'] ?? '');
    }

    public static function getMessageType(): string
    {
        return (string)(self::load()['saas_message_type'] ?? 'info');
    }

    public static function requireJson(string $feature): void
    {
        if (!self::check($feature)) {
            http_response_code(403);
            header('Content-Type: application/json');
            $label = ucwords(str_replace(['feature_', '_'], ['', ' '], $feature));
            echo json_encode([
                'error'   => "El módulo «{$label}» está desactivado por la plataforma SaaS.",
                'feature' => $feature,
                'blocked' => true,
            ]);
            exit;
        }
    }

    public static function requirePage(string $feature): void
    {
        if (!self::check($feature)) {
            $GLOBALS['_blocked_feature'] = $feature;
            $page = __DIR__ . '/../vistas/feature_disabled.php';
            if (file_exists($page)) {
                require $page;
            } else {
                http_response_code(403);
                echo '<h1>Módulo desactivado</h1>';
            }
            exit;
        }
    }

    public static function clearCache(): void
    {
        unset($_SESSION[self::SESSION_TS], $_SESSION[self::SESSION_DATA]);
    }

    public static function getAll(): array
    {
        return self::load();
    }

    public static function isSuspended(): bool
    {
        return (self::load()['instance_status'] ?? 'active') !== 'active';
    }

    public static function getSuspensionMessage(): string
    {
        return (string)(self::load()['suspension_message'] ?? '');
    }
}

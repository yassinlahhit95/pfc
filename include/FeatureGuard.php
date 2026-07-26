<?php
require_once __DIR__ . '/LicenseToken.php';

// Aplicación server-side de feature flags respaldada por un token de licencia firmado.
// Jerarquía de confianza (alta → baja):
//   1. Token válido y no expirado  ← emitido y firmado por el SaaS, no se puede falsificar
//   2. Periodo de gracia (sin token y nunca sincronizado) ← instalación nueva
//   3. Token expirado o manipulado ← FAIL CLOSED (tratado como suspendido)
class FeatureGuard
{
    private const TTL          = 5;               // segundos de caché en sesión (fallback sin APCu)
    private const APCU_TTL     = 60;              // segundos de caché APCu compartida entre workers
    private const APCU_KEY     = 'aulapro_fg';    // clave APCu única por instancia
    private const SESSION_TS   = '_fg_ts';
    private const SESSION_DATA = '_fg_data';

    // ══════════════════════════════════════════════════════════════════════
    // CARGA Y CACHÉ
    // ══════════════════════════════════════════════════════════════════════

    private static function load(): array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // L1: APCu shared memory — 60 s TTL, visible to all PHP-FPM workers.
        // When admin saves config, clearCache() deletes this key so the very
        // next request from any user re-reads from DB and repopulates APCu.
        if (function_exists('apcu_fetch')) {
            $found = false;
            $cached = apcu_fetch(self::APCU_KEY, $found);
            if ($found && is_array($cached)) return $cached;
        }

        // L2: Per-user session cache — 5 s TTL, used when APCu is unavailable.
        if (
            isset($_SESSION[self::SESSION_TS], $_SESSION[self::SESSION_DATA]) &&
            $_SESSION[self::SESSION_TS] > time() - self::TTL
        ) {
            return $_SESSION[self::SESSION_DATA];
        }

        require_once __DIR__ . '/../modelos/conectar.php';
        $con = obtenerConexion();

        // SELECT * avoids breaking when optional columns (added by migrations) don't exist yet.
        $res = mysqli_query($con,
            'SELECT * FROM configuracion_centro WHERE idConfig = 1 LIMIT 1');
        $row = ($res ? mysqli_fetch_assoc($res) : null) ?? [];

        $data = self::resolve($row);

        // Write to both caches so the next request is served from APCu when available.
        if (function_exists('apcu_store')) {
            apcu_store(self::APCU_KEY, $data, self::APCU_TTL);
        }
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
        // El token define el TECHO (qué funcionalidades tiene contratadas la licencia).
        // El toggle de la BD define el SUELO (qué funcionalidades ha activado el admin dentro de lo licenciado).
        // Una funcionalidad está activa solo si la licencia la permite Y el admin la tiene activada.
        if ($rawToken) {
            $payload = LicenseToken::verify($rawToken);
            if ($payload !== null) {
                // tok_* = lo que permite la licencia (1 si la licencia lo incluye, 0 si no)
                // db_*  = lo que el admin ha activado en configuración (puede desactivar lo que la licencia permite)
                $feat = static function(string $key, array $payload, array $row): int {
                    $licensedOn = (bool)($payload['features'][$key] ?? true);
                    $adminOn    = (bool)((int)($row[$key] ?? 1));
                    return (int)($licensedOn && $adminOn);
                };
                return [
                    'instance_status'      => $payload['status']   ?? 'active',
                    'suspension_message'   => $payload['susp_msg'] ?? '',
                    'nombreCentro'         => $row['nombreCentro'] ?: 'AulaPro',
                    'feature_prematricula' => $feat('feature_prematricula', $payload, $row),
                    'feature_chat'         => $feat('feature_chat',         $payload, $row),
                    'feature_inventario'   => $feat('feature_inventario',   $payload, $row),
                    'feature_subida_tfg'   => $feat('feature_subida_tfg',   $payload, $row),
                    'feature_anuncios'     => $feat('feature_anuncios',     $payload, $row),
                    'feature_eventos'      => $feat('feature_eventos',      $payload, $row),
                    'feature_retos'        => $feat('feature_retos',        $payload, $row),
                    'feature_mensajes'     => $feat('feature_mensajes',     $payload, $row),
                    'feature_pagos'        => $feat('feature_pagos',        $payload, $row),
                    'feature_gastos'       => $feat('feature_gastos',       $payload, $row),
                    'feature_informes'     => $feat('feature_informes',     $payload, $row),
                    'feature_horario'      => $feat('feature_horario',      $payload, $row),
                    'feature_fp_dual'      => $feat('feature_fp_dual',      $payload, $row),
                    'feature_ra_ce'        => $feat('feature_ra_ce',        $payload, $row),
                    'feature_landing'      => $feat('feature_landing',      $payload, $row),
                    'feature_academico_config' => $feat('feature_academico_config', $payload, $row),
                    'feature_geoblock_admin' => $feat('feature_geoblock_admin', $payload, $row),
                    'feature_fct'          => $feat('feature_fct',          $payload, $row),
                    'saas_lock_features'   => (int)(bool)($payload['lock']     ?? false),
                    'saas_message'         => $payload['msg']      ?? '',
                    'saas_message_type'    => $payload['msg_type'] ?? 'info',
                    'sub_exp'              => isset($payload['sub_exp']) ? (int)$payload['sub_exp'] : null,
                    '_source'              => 'token',
                ];
            }

            // Token presente pero inválido/expirado → fail closed inmediato (sin gracia)
            return self::suspendedState('Su licencia ha expirado. Contacte con el proveedor para renovarla.', $row);
        }

        // Camino B: sin token — periodo de gracia si NUNCA se licenció
        // license_token_exp solo lo escribe storeLicenseToken (ruta de heartbeat).
        // Si es null → instalación nueva o pre-heartbeat → permitir con valores brutos de BD.
        // Si está definido → un heartbeat corrió antes pero el token ya no está → fail closed.
        $hasEverBeenLicensed = !empty($row['license_token_exp'] ?? null);

        if ($hasEverBeenLicensed) {
            return self::suspendedState('Renovación de licencia requerida. Contacte con el proveedor.', $row);
        }

        // Instalación nueva (columna inexistente aún) o nunca sincronizada → valores brutos de BD
        return [
            'instance_status'      => $row['instance_status']     ?? 'active',
            'suspension_message'   => $row['suspension_message']  ?? '',
            'nombreCentro'         => $row['nombreCentro'] ?: 'AulaPro',
            'feature_prematricula' => (int)($row['feature_prematricula'] ?? 0),
            'feature_chat'         => (int)($row['feature_chat']         ?? 1),
            'feature_inventario'   => (int)($row['feature_inventario']   ?? 1),
            'feature_subida_tfg'   => (int)($row['feature_subida_tfg']   ?? 1),
            'feature_anuncios'     => (int)($row['feature_anuncios']     ?? 1),
            'feature_eventos'      => (int)($row['feature_eventos']      ?? 1),
            'feature_retos'        => (int)($row['feature_retos']        ?? 1),
            'feature_mensajes'     => (int)($row['feature_mensajes']     ?? 1),
            'feature_pagos'        => (int)($row['feature_pagos']        ?? 1),
            'feature_gastos'       => (int)($row['feature_gastos']       ?? 1),
            'feature_informes'     => (int)($row['feature_informes']     ?? 1),
            'feature_horario'      => (int)($row['feature_horario']      ?? 1),
            'feature_fp_dual'      => (int)($row['feature_fp_dual']      ?? 1),
            'feature_ra_ce'        => (int)($row['feature_ra_ce']        ?? 1),
            'feature_landing'      => (int)($row['feature_landing']      ?? 1),
            'feature_academico_config' => (int)($row['feature_academico_config'] ?? 0),
            'feature_geoblock_admin' => (int)($row['feature_geoblock_admin'] ?? 1),
            'feature_fct'          => (int)($row['feature_fct']          ?? 1),
            'saas_lock_features'   => (int)($row['saas_lock_features']   ?? 0),
            'saas_message'         => $row['saas_message']      ?? '',
            'saas_message_type'    => $row['saas_message_type'] ?? 'info',
            'sub_exp'              => null,
            '_source'              => 'grace',
        ];
    }

    private static function suspendedState(string $message, array $row = []): array
    {
        return [
            'instance_status'      => 'suspended',
            'suspension_message'   => $message,
            'nombreCentro'         => $row['nombreCentro'] ?: 'AulaPro',
            'feature_prematricula' => 0,
            'feature_chat'         => 0,
            'feature_inventario'   => 0,
            'feature_subida_tfg'   => 0,
            'feature_anuncios'     => 0,
            'feature_eventos'      => 0,
            'feature_retos'        => 0,
            'feature_mensajes'     => 0,
            'feature_pagos'        => 0,
            'feature_gastos'       => 0,
            'feature_informes'     => 0,
            'feature_horario'      => 0,
            'feature_fp_dual'      => 0,
            'feature_ra_ce'        => 0,
            'feature_landing'      => 0,
            'feature_academico_config' => 0,
            'feature_geoblock_admin' => 0,
            'feature_fct'          => 0,
            'saas_lock_features'   => 1,
            'saas_message'         => '',
            'saas_message_type'    => 'info',
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
        // Clear APCu first — this immediately invalidates the cache for all workers.
        if (function_exists('apcu_delete')) {
            apcu_delete(self::APCU_KEY);
        }
        unset($_SESSION[self::SESSION_TS], $_SESSION[self::SESSION_DATA]);
    }

    public static function getAll(): array
    {
        return self::load();
    }

    // Nombre del centro configurado en el onboarding/ajustes (configuracion_centro.nombreCentro).
    // Reutiliza la misma carga cacheada (APCu 60s) que ya hace load() para los
    // feature flags, en vez de otra consulta aparte — y ya se invalida sola
    // cuando guardarConfiguracionCentro() llama a clearCache() al guardar.
    public static function getCenterName(): string
    {
        return (string)(self::load()['nombreCentro'] ?? 'AulaPro');
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

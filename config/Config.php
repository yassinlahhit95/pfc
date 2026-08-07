<?php
/**
 * Gestión centralizada de configuración.
 * Carga variables desde .env, variables de entorno del sistema o db.php (fallback para hosting compartido).
 */

class Config {
    private static $instance = null;
    private $config = [];
    private $env    = [];

    private function __construct() {
        $this->loadEnvironmentVariables();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadEnvironmentVariables() {
        // Buscar .env en múltiples ubicaciones posibles según el entorno de despliegue
        $candidates = [
            __DIR__ . '/../.env',
            dirname(__DIR__) . '/.env',
        ];
        // DOCUMENT_ROOT no está definido en CLI (cron, scripts de mantenimiento).
        if (!empty($_SERVER['DOCUMENT_ROOT'])) {
            $candidates[] = $_SERVER['DOCUMENT_ROOT'] . '/.env';
        }
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $this->loadEnvFile($path);
                break;
            }
        }

        // Fallback a db.php para hosting compartido donde el .env no es accesible
        $dbFile = __DIR__ . '/db.php';
        if (file_exists($dbFile)) require_once $dbFile;

        // [MULTI-TENANT DYNAMIC ROUTING]
        // Resolve tenant database based on subdomain (e.g., colegio1.aulapro.test -> yassjjzw_pfc_colegio1)
        $httpHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $httpHost = explode(':', $httpHost)[0];
        $hostParts = explode('.', $httpHost);
        $subdomain = (count($hostParts) >= 3 && $hostParts[0] !== 'www') ? $hostParts[0] : 'default';
        // The Host header is attacker-controlled. Before it ever reaches a DB name or
        // an APCu/R2 key prefix, restrict it to the same charset a real subdomain can
        // ever contain — anything else (injected chars, absurd length) falls back to
        // 'default' rather than being used verbatim. This doesn't confirm the subdomain
        // maps to a *real* provisioned tenant (that list lives in saas-admin, a separate
        // service/DB this app doesn't call on every request) — it only guarantees the
        // value can't smuggle anything unexpected into a database/cache name.
        if (!preg_match('/^[a-z0-9-]{1,63}$/', $subdomain)) {
            $subdomain = 'default';
        }

        $dynamicDbName = "yassjjzw_pfc_" . str_replace('-', '_', $subdomain);
        
        // Database
        $this->config['DB_HOST'] = $this->env('DB_HOST', defined('DB_HOST_VALUE') ? DB_HOST_VALUE : 'localhost');
        $this->config['DB_USER'] = $this->env('DB_USER', defined('DB_USER_VALUE') ? DB_USER_VALUE : 'root');
        $this->config['DB_PASS'] = $this->env('DB_PASS', defined('DB_PASS_VALUE') ? DB_PASS_VALUE : '');
        // If the dynamic DB exists/applies, use it, otherwise fallback to default env
        $this->config['DB_NAME'] = $this->env('DB_NAME', defined('DB_NAME_VALUE') ? DB_NAME_VALUE : $dynamicDbName);

        // Tenant Prefix for R2 isolated file storage. Prefer an explicit .env
        // value (set once, automatically, by api/admin.php's pairing step) over
        // the Host-header-derived subdomain — the latter only actually varies
        // per client on a subdomain-per-tenant deployment (client1.aulapro.tld).
        // A white-label client on their own bare custom domain (client1.tld,
        // 2 labels) always resolves $subdomain to the literal 'default', same
        // as every other bare-domain client — silently merging all of their R2
        // usage/quota tracking into one shared namespace. An explicit prefix
        // is guaranteed unique regardless of DNS topology.
        $this->config['R2_TENANT_PREFIX'] = $this->env('R2_TENANT_PREFIX', $subdomain);

        // Firebase
        $this->config['FIREBASE_API_KEY']            = $this->env('FIREBASE_API_KEY', '');
        $this->config['FIREBASE_AUTH_DOMAIN']         = $this->env('FIREBASE_AUTH_DOMAIN', '');
        $this->config['FIREBASE_PROJECT_ID']          = $this->env('FIREBASE_PROJECT_ID', '');
        $this->config['FIREBASE_MESSAGING_SENDER_ID'] = $this->env('FIREBASE_MESSAGING_SENDER_ID', '');
        $this->config['FIREBASE_APP_ID']              = $this->env('FIREBASE_APP_ID', '');
        $this->config['FIREBASE_DATABASE_URL']        = $this->env('FIREBASE_DATABASE_URL', '');
        $this->config['FIREBASE_VAPID_KEY']           = $this->env('FIREBASE_VAPID_KEY', '');

        // Brevo
        $this->config['BREVO_API_KEY'] = $this->env('BREVO_API_KEY', '');

        // El secreto del QR del boletín debe estar en .env; nunca hardcodeado
        $this->config['BOLETIN_SECRET'] = $this->env('BOLETIN_SECRET', '');

        // Clave maestra de cifrado de datos personales (RGPD Art. 32). Nunca hardcodeada.
        // Sin fallback aleatorio: si falta, Crypto debe fallar fuerte en vez de
        // cifrar con una clave distinta en cada request.
        $this->config['PII_ENCRYPTION_KEY'] = $this->env('PII_ENCRYPTION_KEY', '');

        // Cloudflare R2 (almacenamiento de ficheros subidos, S3-compatible).
        // Sin valor por defecto: la app debe seguir funcionando con estos vacíos
        // (los ficheros ya existentes en public/uploads/ siguen sirviéndose desde
        // disco local) — R2Client falla fuerte solo cuando de verdad se invoca.
        $this->config['R2_ACCOUNT_ID']        = $this->env('R2_ACCOUNT_ID', '');
        $this->config['R2_ACCESS_KEY_ID']     = $this->env('R2_ACCESS_KEY_ID', '');
        $this->config['R2_SECRET_ACCESS_KEY'] = $this->env('R2_SECRET_ACCESS_KEY', '');
        $this->config['R2_BUCKET_NAME']       = $this->env('R2_BUCKET_NAME', '');
        $this->config['R2_PUBLIC_URL']        = rtrim($this->env('R2_PUBLIC_URL', ''), '/');

        // SaaS Admin integration — must match the api_key/api_secret of the
        // `connections` row saas-admin has for this instance. Also doubles as
        // the inbound HMAC auth secret for api/admin.php's suspend/activate/
        // heartbeat endpoints and signs LicenseToken.php's tokens — those two
        // files parse .env directly instead of going through Config, which is
        // why this being missing here went unnoticed: vistas/auth/diagnostico_email.php
        // was the only caller that actually went through
        // Config::get('ADMIN_API_KEY'/'ADMIN_API_SECRET'), and it silently got
        // '' back on every request since this class never populated the key.
        $this->config['SAAS_ADMIN_URL']    = rtrim($this->env('SAAS_ADMIN_URL', ''), '/');
        $this->config['ADMIN_API_KEY']     = $this->env('ADMIN_API_KEY', '');
        $this->config['ADMIN_API_SECRET']  = $this->env('ADMIN_API_SECRET', '');

        // Application
        // URL pública canónica (p. ej. https://aulapro.yassin.agency). Se usa para
        // construir enlaces en emails y evitar inyección de cabecera Host.
        $this->config['APP_URL']         = rtrim($this->env('APP_URL', ''), '/');
        $this->config['APP_ENV']         = $this->env('APP_ENV', 'development');
        $this->config['APP_DEBUG']        = filter_var($this->env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
        $this->config['SESSION_TIMEOUT']  = intval($this->env('SESSION_TIMEOUT', '3600'));
        $this->config['GOOGLE_CLIENT_ID'] = $this->env('GOOGLE_CLIENT_ID', '');
    }

    private function loadEnvFile($path) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (strpos($line, '=') === false)     continue;

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Eliminar comillas envolventes si las tiene
            if (strlen($value) >= 2) {
                $q = $value[0];
                if (($q === '"' || $q === "'") && $value[-1] === $q) {
                    $value = substr($value, 1, -1);
                }
            }

            if ($key !== '') {
                $this->env[$key] = $value;
                putenv("$key=$value");
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    // Prioridad: .env parseado → variable de entorno del sistema → valor por defecto
    private function env($key, $default = '') {
        if (isset($this->env[$key]) && $this->env[$key] !== '') return $this->env[$key];
        $v = getenv($key);
        if ($v !== false && $v !== '') return $v;
        return $default;
    }

    public function get($key, $default = null) {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    public function getBoolean($key, $default = false) {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    public function getInteger($key, $default = 0) {
        return intval($this->get($key, $default));
    }

    public function isDebug() {
        return $this->getBoolean('APP_DEBUG');
    }

    public function isProduction() {
        return $this->get('APP_ENV') === 'production';
    }
}

$config = Config::getInstance();
?>

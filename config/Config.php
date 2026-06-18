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
            $_SERVER['DOCUMENT_ROOT'] . '/.env',
        ];
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $this->loadEnvFile($path);
                break;
            }
        }

        // Fallback a db.php para hosting compartido donde el .env no es accesible
        $dbFile = __DIR__ . '/db.php';
        if (file_exists($dbFile)) require_once $dbFile;

        // Database
        $this->config['DB_HOST'] = $this->env('DB_HOST', defined('DB_HOST_VALUE') ? DB_HOST_VALUE : 'localhost');
        $this->config['DB_USER'] = $this->env('DB_USER', defined('DB_USER_VALUE') ? DB_USER_VALUE : '');
        $this->config['DB_PASS'] = $this->env('DB_PASS', defined('DB_PASS_VALUE') ? DB_PASS_VALUE : '');
        $this->config['DB_NAME'] = $this->env('DB_NAME', defined('DB_NAME_VALUE') ? DB_NAME_VALUE : 'yassjjzw_pfc');

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

        // Application
        // URL pública canónica (p. ej. https://aulapro.yassin.agency). Se usa para
        // construir enlaces en emails y evitar inyección de cabecera Host.
        $this->config['APP_URL']         = rtrim($this->env('APP_URL', ''), '/');
        $this->config['APP_ENV']         = $this->env('APP_ENV', 'development');
        $this->config['APP_DEBUG']        = filter_var($this->env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);
        $this->config['SESSION_TIMEOUT']  = intval($this->env('SESSION_TIMEOUT', '3600'));
        $this->config['APP_KEY']          = $this->env('APP_KEY', '') ?: $this->generateAppKey();
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

    private function generateAppKey() {
        return bin2hex(random_bytes(32));
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

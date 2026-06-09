<?php
/**
 * Configuración centralizada y segura del sistema
 * Carga variables desde .env o environment
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
        // Try multiple possible .env locations
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

        // Load PHP credentials file as fallback (more reliable than .env on shared hosting)
        $dbFile = __DIR__ . '/db.php';
        if (file_exists($dbFile)) require_once $dbFile;

        // Database
        $this->config['DB_HOST'] = $this->env('DB_HOST', defined('DB_HOST_VALUE') ? DB_HOST_VALUE : 'localhost');
        $this->config['DB_USER'] = $this->env('DB_USER', defined('DB_USER_VALUE') ? DB_USER_VALUE : '');
        $this->config['DB_PASS'] = $this->env('DB_PASS', defined('DB_PASS_VALUE') ? DB_PASS_VALUE : '');
        $this->config['DB_NAME'] = $this->env('DB_NAME', defined('DB_NAME_VALUE') ? DB_NAME_VALUE : 'yassjjzw_pfc');

        // Firebase
        $this->config['FIREBASE_API_KEY']    = $this->env('FIREBASE_API_KEY', '');
        $this->config['FIREBASE_AUTH_DOMAIN'] = $this->env('FIREBASE_AUTH_DOMAIN', '');
        $this->config['FIREBASE_PROJECT_ID']  = $this->env('FIREBASE_PROJECT_ID', '');

        // Brevo
        $this->config['BREVO_API_KEY'] = $this->env('BREVO_API_KEY', '');

        // Boletín QR secret — must be set in .env, never hardcoded
        $this->config['BOLETIN_SECRET'] = $this->env('BOLETIN_SECRET', '');

        // Application
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

            // Strip surrounding quotes if present
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

    // Read from parsed .env first, then system env, then default
    private function env($key, $default = '') {
        if (isset($this->env[$key]) && $this->env[$key] !== '') return $this->env[$key];
        $v = getenv($key);
        if ($v !== false && $v !== '') return $v;
        return $default;
    }

    /**
     * Obtiene valor de configuración
     */
    public function get($key, $default = null) {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * Obtiene valor booleano
     */
    public function getBoolean($key, $default = false) {
        return filter_var($this->get($key, $default), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Obtiene valor entero
     */
    public function getInteger($key, $default = 0) {
        return intval($this->get($key, $default));
    }

    /**
     * Genera clave de aplicación aleatoria
     */
    private function generateAppKey() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Verifica si está en modo debug
     */
    public function isDebug() {
        return $this->getBoolean('APP_DEBUG');
    }

    /**
     * Verifica si está en producción
     */
    public function isProduction() {
        return $this->get('APP_ENV') === 'production';
    }
}

// Singleton instance
$config = Config::getInstance();
?>

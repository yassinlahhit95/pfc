<?php
/**
 * Configuración centralizada y segura del sistema
 * Carga variables desde .env o environment
 */

class Config {
    private static $instance = null;
    private $config = [];

    private function __construct() {
        $this->loadEnvironmentVariables();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Carga variables de entorno desde .env o variables del sistema
     */
    private function loadEnvironmentVariables() {
        // Cargar desde archivo .env si existe
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $this->loadEnvFile($envFile);
        }

        // Database
        $this->config['DB_HOST'] = getenv('DB_HOST') ?: 'localhost';
        $this->config['DB_USER'] = getenv('DB_USER') ?: '';
        $this->config['DB_PASS'] = getenv('DB_PASS') ?: '';
        $this->config['DB_NAME'] = getenv('DB_NAME') ?: 'aulapro';

        // Firebase
        $this->config['FIREBASE_API_KEY'] = getenv('FIREBASE_API_KEY') ?: '';
        $this->config['FIREBASE_AUTH_DOMAIN'] = getenv('FIREBASE_AUTH_DOMAIN') ?: '';
        $this->config['FIREBASE_PROJECT_ID'] = getenv('FIREBASE_PROJECT_ID') ?: '';

        // Brevo
        $this->config['BREVO_API_KEY'] = getenv('BREVO_API_KEY') ?: '';

        // Application
        $this->config['APP_ENV'] = getenv('APP_ENV') ?: 'development';
        $this->config['APP_DEBUG'] = filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
        $this->config['SESSION_TIMEOUT'] = intval(getenv('SESSION_TIMEOUT') ?: 3600);
        $this->config['APP_KEY'] = getenv('APP_KEY') ?: $this->generateAppKey();
    }

    /**
     * Carga variables desde archivo .env
     */
    private function loadEnvFile($path) {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Ignorar comentarios
            }

            if (strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!empty($key)) {
                putenv("$key=$value");
            }
        }
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

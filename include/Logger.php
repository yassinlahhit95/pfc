<?php
/**
 * Sistema centralizado de logging
 * Registra errores, accesos, actividades en archivos seguros
 */

class Logger {
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_INFO = 'INFO';
    const LEVEL_DEBUG = 'DEBUG';
    const LEVEL_ACTIVITY = 'ACTIVITY';

    private static $logDir = null;
    private static $isInitialized = false;

    public static function init($logDirectory = null) {
        if (self::$isInitialized) {
            return;
        }

        // Configurar directorio de logs
        if ($logDirectory === null) {
            $logDirectory = __DIR__ . '/../logs';
        }

        self::$logDir = $logDirectory;

        // Crear directorio si no existe
        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }

        self::$isInitialized = true;
    }

    /**
     * Log de error
     */
    public static function error($message, $context = []) {
        self::log(self::LEVEL_ERROR, $message, $context, 'error.log');
    }

    /**
     * Log de advertencia
     */
    public static function warning($message, $context = []) {
        self::log(self::LEVEL_WARNING, $message, $context, 'warning.log');
    }

    /**
     * Log de información
     */
    public static function info($message, $context = []) {
        self::log(self::LEVEL_INFO, $message, $context, 'info.log');
    }

    /**
     * Log de debug
     */
    public static function debug($message, $context = []) {
        self::log(self::LEVEL_DEBUG, $message, $context, 'debug.log');
    }

    /**
     * Log de actividad de usuario
     */
    public static function activity($action, $userId = null, $details = []) {
        $context = array_merge(['user_id' => $userId], $details);
        self::log(self::LEVEL_ACTIVITY, "Action: $action", $context, 'activity.log');
    }

    /**
     * Log de acceso
     */
    public static function access($path, $method = 'GET', $statusCode = 200, $userId = null) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100);

        $message = "$method $path [$statusCode] - IP: $ip";
        $context = [
            'ip' => $ip,
            'method' => $method,
            'path' => $path,
            'status_code' => $statusCode,
            'user_id' => $userId,
            'user_agent' => $userAgent
        ];

        self::log(self::LEVEL_INFO, $message, $context, 'access.log');
    }

    /**
     * Log de seguridad (intentos fallidos, etc)
     */
    public static function security($event, $details = []) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $context = array_merge(['ip' => $ip], $details);
        self::log(self::LEVEL_WARNING, "Security Event: $event", $context, 'security.log');
    }

    /**
     * Función central de logging
     */
    private static function log($level, $message, $context = [], $filename = 'app.log') {
        if (!self::$isInitialized) {
            self::init();
        }

        $filepath = self::$logDir . '/' . $filename;

        // Formatear mensaje
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        // Escribir en archivo
        error_log($logMessage, 3, $filepath);

        // Si es error crítico, también guardar en error.log del sistema
        if ($level === self::LEVEL_ERROR) {
            error_log($logMessage, 3, self::$logDir . '/critical.log');
        }
    }

    /**
     * Obtiene últimas líneas del log
     */
    public static function getTail($filename = 'error.log', $lines = 50) {
        if (!self::$isInitialized) {
            self::init();
        }

        $filepath = self::$logDir . '/' . $filename;

        if (!file_exists($filepath)) {
            return [];
        }

        $file = new \SplFileObject($filepath, 'r');
        $file->seek(\PHP_INT_MAX);
        $lastLine = $file->key();

        $result = [];
        for ($i = max(0, $lastLine - $lines); $i <= $lastLine; $i++) {
            $file->seek($i);
            $line = $file->current();
            if (!empty(trim($line))) {
                $result[] = $line;
            }
        }

        return $result;
    }

    /**
     * Limpia logs antiguos
     */
    public static function cleanup($daysOld = 30) {
        if (!self::$isInitialized) {
            self::init();
        }

        $cutoffTime = time() - (86400 * $daysOld);

        foreach (glob(self::$logDir . '/*.log') as $file) {
            if (is_file($file) && filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }
}

// Inicializar Logger
Logger::init();
?>

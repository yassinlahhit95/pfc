<?php
class Logger {

    // ══════════════════════════════════════════════════════════════════════
    // CONFIGURACIÓN
    // ══════════════════════════════════════════════════════════════════════

    const LEVEL_ERROR    = 'ERROR';
    const LEVEL_WARNING  = 'WARNING';
    const LEVEL_INFO     = 'INFO';
    const LEVEL_DEBUG    = 'DEBUG';
    const LEVEL_ACTIVITY = 'ACTIVITY';

    private static $logDir = null;
    private static $isInitialized = false;

    // ══════════════════════════════════════════════════════════════════════
    // INICIALIZACIÓN
    // ══════════════════════════════════════════════════════════════════════

    public static function init($logDirectory = null) {
        if (self::$isInitialized) {
            return;
        }

        if ($logDirectory === null) {
            $logDirectory = __DIR__ . '/../logs';
        }

        self::$logDir = $logDirectory;

        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }

        self::$isInitialized = true;
    }

    // ══════════════════════════════════════════════════════════════════════
    // MÉTODOS PÚBLICOS
    // ══════════════════════════════════════════════════════════════════════

    public static function error($message, $context = []) {
        self::log(self::LEVEL_ERROR, $message, $context, 'error.log');
    }

    public static function warning($message, $context = []) {
        self::log(self::LEVEL_WARNING, $message, $context, 'warning.log');
    }

    public static function info($message, $context = []) {
        self::log(self::LEVEL_INFO, $message, $context, 'info.log');
    }

    public static function debug($message, $context = []) {
        self::log(self::LEVEL_DEBUG, $message, $context, 'debug.log');
    }

    public static function activity($action, $userId = null, $details = []) {
        $context = array_merge(['user_id' => $userId], $details);
        self::log(self::LEVEL_ACTIVITY, "Acción: $action", $context, 'activity.log');
    }

    public static function access($path, $method = 'GET', $statusCode = 200, $userId = null) {
        $ip        = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 100);

        $message = "$method $path [$statusCode] - IP: $ip";
        $context = [
            'ip'          => $ip,
            'method'      => $method,
            'path'        => $path,
            'status_code' => $statusCode,
            'user_id'     => $userId,
            'user_agent'  => $userAgent,
        ];

        self::log(self::LEVEL_INFO, $message, $context, 'access.log');
    }

    public static function security($event, $details = []) {
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'desconocida';
        $context = array_merge(['ip' => $ip], $details);
        self::log(self::LEVEL_WARNING, "Evento de seguridad: $event", $context, 'security.log');
    }

    // ══════════════════════════════════════════════════════════════════════
    // ESCRITURA
    // ══════════════════════════════════════════════════════════════════════

    private static function log($level, $message, $context = [], $filename = 'app.log') {
        if (!self::$isInitialized) {
            self::init();
        }

        $filepath   = self::$logDir . '/' . $filename;
        $timestamp  = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        error_log($logMessage, 3, $filepath);

        // Los errores críticos se duplican en critical.log para facilitar el diagnóstico
        if ($level === self::LEVEL_ERROR) {
            error_log($logMessage, 3, self::$logDir . '/critical.log');
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // UTILIDADES
    // ══════════════════════════════════════════════════════════════════════

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

}

Logger::init();

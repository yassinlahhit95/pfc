<?php
/**
 * Bootstrap para ejecución de tests
 * Configura el entorno de pruebas
 */

// Definir constante de TESTING
const TESTING = true;

// Cargar directorios del proyecto
define('PROJECT_ROOT', dirname(__DIR__));

// Composer autoloader (dompdf, masterminds/html5, phenx, sabberworm, etc.)
if (file_exists(PROJECT_ROOT . '/vendor/autoload.php')) {
    require_once PROJECT_ROOT . '/vendor/autoload.php';
}

// Autoloading de clases
spl_autoload_register(function($class) {
    $basePaths = [
        PROJECT_ROOT . '/include/',
        PROJECT_ROOT . '/config/',
        PROJECT_ROOT . '/modelos/',
        PROJECT_ROOT . '/controladores/',
        PROJECT_ROOT . '/templates/',
    ];

    // Build full list including all subdirectories recursively
    $paths = [];
    foreach ($basePaths as $base) {
        if (!is_dir($base)) continue;
        $paths[] = $base;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $paths[] = $item->getPathname() . '/';
            }
        }
    }

    foreach ($paths as $path) {
        $file = "{$path}{$class}.php";
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Cargar clases de seguridad y configuración
require_once PROJECT_ROOT . '/include/Security.php';
require_once PROJECT_ROOT . '/include/Logger.php';
require_once PROJECT_ROOT . '/config/Config.php';

// Configurar para tests
putenv('APP_ENV=testing');
putenv('APP_DEBUG=true');

// Error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

echo "Bootstrap completado. Entorno de testing inicializado.\n";

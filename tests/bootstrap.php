<?php
/**
 * Bootstrap para ejecución de tests
 * Configura el entorno de pruebas
 */

// Definir constante de TESTING
define('TESTING', true);

// Cargar directorios del proyecto
define('PROJECT_ROOT', dirname(__DIR__));

// Autoloading de clases
spl_autoload_register(function($class) {
    $paths = [
        PROJECT_ROOT . '/include/',
        PROJECT_ROOT . '/config/',
        PROJECT_ROOT . '/modelos/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
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
?>

<?php
// Diagnostic script for debugging 500 errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1 style='color: #333;'>🔍 AulaPro Diagnostic Report</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.error { color: #d32f2f; background: #ffebee; padding: 10px; margin: 10px 0; border-radius: 4px; }
.success { color: #388e3c; background: #e8f5e9; padding: 10px; margin: 10px 0; border-radius: 4px; }
.warning { color: #f57c00; background: #fff3e0; padding: 10px; margin: 10px 0; border-radius: 4px; }
code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
</style>";

// 1. Check .env file
echo "<h2>1. Environment Configuration</h2>";
if (file_exists('.env')) {
    echo "<div class='success'>✅ .env file exists</div>";
    $env_content = file_get_contents('.env');
    preg_match('/DB_HOST=(.+)/', $env_content, $host);
    preg_match('/DB_NAME=(.+)/', $env_content, $db);
    preg_match('/APP_URL=(.+)/', $env_content, $url);
    echo "<p><strong>DB_HOST:</strong> " . (isset($host[1]) ? htmlspecialchars($host[1]) : "NOT SET") . "</p>";
    echo "<p><strong>DB_NAME:</strong> " . (isset($db[1]) ? htmlspecialchars($db[1]) : "NOT SET") . "</p>";
    echo "<p><strong>APP_URL:</strong> " . (isset($url[1]) ? htmlspecialchars($url[1]) : "NOT SET") . "</p>";
} else {
    echo "<div class='error'>❌ .env file NOT found - This is the problem!</div>";
    echo "<p>You need to create .env with database credentials on production</p>";
}

// 2. Check Config class
echo "<h2>2. Configuration Class</h2>";
try {
    require_once 'config/Config.php';
    echo "<div class='success'>✅ Config class loads</div>";
    $config = Config::getInstance();
    echo "<p>DB_USER configured: " . (!empty($config->get('DB_USER')) ? "✅ Yes" : "❌ No") . "</p>";
    echo "<p>DB_PASS configured: " . (!empty($config->get('DB_PASS')) ? "✅ Yes" : "❌ No") . "</p>";
} catch (Throwable $e) {
    echo "<div class='error'>❌ Config Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

// 3. Check database connection
echo "<h2>3. Database Connection</h2>";
try {
    require_once 'modelos/conectar.php';
    $con = obtenerConexion();
    echo "<div class='success'>✅ Database connected</div>";

    // Check tables
    echo "<h3>Database Tables</h3>";
    $tables_to_check = [
        'academic_config',
        'academic_periods',
        'assessment_types',
        'grading_policies',
        'promotion_rules',
        'internship_config',
        'tfg_config',
        'challenge_config',
        'retos',
        'modulo_reto',
        'reto_archivos'
    ];

    foreach ($tables_to_check as $table) {
        $result = mysqli_query($con, "SELECT 1 FROM `$table` LIMIT 1");
        if ($result) {
            echo "<div class='success'>✅ Table <code>$table</code> exists</div>";
        } else {
            echo "<div class='error'>❌ Table <code>$table</code> is missing!</div>";
        }
    }

    // Don't close connection yet - it will be used by functions below
} catch (Throwable $e) {
    echo "<div class='error'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<p>Check your .env file - DB credentials might be wrong</p>";
}

// 4. Check required files
echo "<h2>4. Required Files</h2>";
$files_to_check = [
    'modelos/retos.php',
    'modelos/academico_config.php',
    'modelos/plantillas_academicas.php',
    'modelos/ciclos.php',
    'include/AdminGuard.php',
    'include/FeatureGuard.php',
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<div class='success'>✅ <code>$file</code></div>";
    } else {
        echo "<div class='error'>❌ <code>$file</code> NOT FOUND</div>";
    }
}

// 5. Test retos.php functions
echo "<h2>5. Test Functions</h2>";
try {
    require_once 'modelos/retos.php';
    $retos = listarRetos();
    echo "<div class='success'>✅ listarRetos() works - returned " . count($retos) . " items</div>";

    if (!empty($retos)) {
        $reto = $retos[0];
        $modulos = listarModulosDeReto($reto['idReto']);
        echo "<div class='success'>✅ listarModulosDeReto() works - returned " . count($modulos) . " items</div>";

        $archivos = obtenerArchivosReto($reto['idReto']);
        echo "<div class='success'>✅ obtenerArchivosReto() works - returned " . count($archivos) . " items</div>";
    }
} catch (Throwable $e) {
    echo "<div class='error'>❌ Function Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<hr>";
echo "<p style='color: #666; font-size: 12px;'>This diagnostic was run at " . date('Y-m-d H:i:s') . "</p>";
?>

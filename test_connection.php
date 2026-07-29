<?php
// Simple diagnostic script — check database connection and table structure
require_once __DIR__ . '/config/Config.php';

$config = Config::getInstance();
$host = $config->get('DB_HOST', 'localhost');
$user = $config->get('DB_USER');
$pass = $config->get('DB_PASS');
$db   = $config->get('DB_NAME', 'aulapro');

echo "<h1>AulaPro Database Diagnostic</h1>";
echo "<p><strong>Host:</strong> $host</p>";
echo "<p><strong>Database:</strong> $db</p>";
echo "<p><strong>User:</strong> " . (empty($user) ? "NOT SET" : "***") . "</p>";

try {
    $con = @mysqli_connect('p:' . $host, $user, $pass, $db);
    if (!$con) {
        echo "<p style='color:red;'><strong>❌ Connection Failed:</strong> " . mysqli_connect_error() . "</p>";
        exit;
    }
    echo "<p style='color:green;'><strong>✅ Connected successfully</strong></p>";
    mysqli_set_charset($con, "utf8mb4");

    // Check critical tables
    $tables = ['retos', 'modulo_reto', 'reto_archivos', 'academic_config', 'modulos'];
    foreach ($tables as $table) {
        $result = mysqli_query($con, "SELECT COUNT(*) as cnt FROM `$table` LIMIT 1");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p style='color:green;'>✅ Table <code>$table</code> exists (" . $row['cnt'] . " rows)</p>";
        } else {
            echo "<p style='color:red;'>❌ Table <code>$table</code> missing: " . mysqli_error($con) . "</p>";
        }
    }

    // Test a sample query
    echo "<h3>Testing retos.php functions:</h3>";
    require_once __DIR__ . '/modelos/retos.php';
    $retos = listarRetos();
    echo "<p>listarRetos() returned: " . count($retos) . " items</p>";

    mysqli_close($con);
} catch (Throwable $e) {
    echo "<p style='color:red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>

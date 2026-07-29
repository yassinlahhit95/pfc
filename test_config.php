<?php
// Simple test - just check if the pages can be included
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Testing configuracionAcademica.php includes</h1>";
echo "<style>body { font-family: Arial; margin: 20px; } .err { color: red; } .ok { color: green; }</style>";

try {
    echo "<p class='ok'>✅ Starting includes...</p>";

    require_once __DIR__ . "/include/AdminGuard.php";
    echo "<p class='ok'>✅ AdminGuard.php loaded</p>";

    require_once __DIR__ . "/modelos/conectar.php";
    echo "<p class='ok'>✅ conectar.php loaded</p>";

    require_once __DIR__ . "/modelos/academico_config.php";
    echo "<p class='ok'>✅ academico_config.php loaded</p>";

    require_once __DIR__ . "/modelos/plantillas_academicas.php";
    echo "<p class='ok'>✅ plantillas_academicas.php loaded</p>";

    require_once __DIR__ . "/modelos/ciclos.php";
    echo "<p class='ok'>✅ ciclos.php loaded</p>";

    // Now try the actual queries
    echo "<h2>Testing queries:</h2>";

    $config = obtenerConfigAcademicaActiva();
    echo "<p class='ok'>✅ obtenerConfigAcademicaActiva() executed</p>";

    if ($config) {
        echo "<p>Found config: " . htmlspecialchars($config['nombre'] ?? 'unnamed') . "</p>";
    } else {
        echo "<p class='err'>⚠️ No active config found (this might be OK)</p>";
    }

    $allConfigs = listarTodasConfiguracionesAcademicas();
    echo "<p class='ok'>✅ listarTodasConfiguracionesAcademicas() returned " . count($allConfigs) . " configs</p>";

} catch (Throwable $e) {
    echo "<p class='err'><strong>❌ ERROR:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>

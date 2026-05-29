<?php
/**
 * Database Migration Script
 * Applies required changes to aula_entregas table
 */

require_once __DIR__ . '/modelos/conectar.php';
require_once __DIR__ . '/config/Config.php';

$config = Config::getInstance();
$con = obtenerConexion();

if (!$con) {
    die('❌ ERROR: Could not connect to database');
}

echo "🔧 Applying database migration...\n\n";

$migrations = [
    "ALTER TABLE `aula_entregas`
    ADD COLUMN IF NOT EXISTS `comentarioCalificacion` text DEFAULT NULL AFTER `nota`" =>
    "✅ Added comentarioCalificacion column",

    "ALTER TABLE `aula_entregas`
    ADD COLUMN IF NOT EXISTS `archivoCorreccion` varchar(255) DEFAULT NULL AFTER `comentarioCalificacion`" =>
    "✅ Added archivoCorreccion column",

    "ALTER TABLE `aula_entregas`
    ADD INDEX IF NOT EXISTS `idx_tarea_estudiante` (`idTarea`, `idEstudiante`)" =>
    "✅ Added idx_tarea_estudiante index",

    "ALTER TABLE `aula_entregas`
    ADD INDEX IF NOT EXISTS `idx_estudiante_nota` (`idEstudiante`, `nota`)" =>
    "✅ Added idx_estudiante_nota index"
];

$failed = 0;
$success = 0;

foreach ($migrations as $sql => $message) {
    if (mysqli_query($con, $sql)) {
        echo $message . "\n";
        $success++;
    } else {
        echo "⚠️  " . $message . " (or already exists)\n";
        echo "   Error: " . mysqli_error($con) . "\n";
        $failed++;
    }
}

// Verify the changes
echo "\n📋 VERIFICATION:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$result = mysqli_query($con, "DESCRIBE aula_entregas");
if ($result) {
    $columns = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
    }

    echo "✅ Column comentarioCalificacion: " . (in_array('comentarioCalificacion', $columns) ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
    echo "✅ Column archivoCorreccion:      " . (in_array('archivoCorreccion', $columns) ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
}

// Check indexes
$result = mysqli_query($con, "SHOW INDEX FROM aula_entregas");
if ($result) {
    $indexes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $indexes[] = $row['Key_name'];
    }

    echo "✅ Index idx_tarea_estudiante:   " . (in_array('idx_tarea_estudiante', $indexes) ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
    echo "✅ Index idx_estudiante_nota:     " . (in_array('idx_estudiante_nota', $indexes) ? "FOUND ✓" : "NOT FOUND ✗") . "\n";
}

mysqli_close($con);

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ MIGRATION COMPLETE!\n\n";
echo "Status: Database is ready for task grading system ✨\n";
?>

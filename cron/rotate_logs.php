<?php
/**
 * cron/rotate_logs.php
 * Log rotation script — deletes logs older than 30 days
 * Run weekly: 0 2 * * 0 php /path/to/cron/rotate_logs.php
 */

if (PHP_SAPI !== 'cli' && ($_SERVER['REMOTE_ADDR'] ?? '') !== '127.0.0.1') {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../modelos/conectar.php';

$logsDir = __DIR__ . '/../logs/';
$retentionDays = 30;
$cutoffTime = time() - ($retentionDays * 86400);

$deleted = 0;
$totalSize = 0;

if (is_dir($logsDir)) {
    $files = glob($logsDir . '*.log');
    foreach ($files as $file) {
        if (is_file($file) && filemtime($file) < $cutoffTime) {
            $fileSize = filesize($file);
            if (@unlink($file)) {
                $deleted++;
                $totalSize += $fileSize;
                echo "Deleted: $file (" . number_format($fileSize, 0) . " bytes)\n";
            }
        }
    }
}

echo "Log rotation complete: $deleted files deleted, " . number_format($totalSize, 0) . " bytes freed.\n";

// Log rotation execution
$con = obtenerConexion();
if ($con) {
    $status = 'success';
    $errorMsg = "Cleaned up $deleted log files";
    $sql = "INSERT INTO cron_execution_log (job_name, last_run, last_run_status, error_message)
            VALUES ('rotate_logs.php', NOW(), ?, ?)
            ON DUPLICATE KEY UPDATE last_run = NOW(), last_run_status = ?, error_message = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $status, $errorMsg, $status, $errorMsg);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

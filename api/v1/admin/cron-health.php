<?php
declare(strict_types=1);

// GET /api/v1/admin/cron-health
// Returns status of scheduled cron jobs

require_once __DIR__ . '/../_api.php';
require_once __DIR__ . '/../../../include/AdminGuard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$usuario = v1Auth();

// Only admins can check cron health
if ($usuario['user_type'] !== 'admin') {
    v1Error('Forbidden', 403, 'access_denied');
}

require_once __DIR__ . '/../../../modelos/conectar.php';

$con = obtenerConexion();
$sql = "SELECT job_name, last_run, last_run_status, error_message
        FROM cron_execution_log
        ORDER BY job_name";

$result = mysqli_query($con, $sql);
$jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);

$health = [];
$now = new DateTime();

foreach ($jobs as $job) {
    $lastRun = $job['last_run'] ? new DateTime($job['last_run']) : null;
    $hoursSinceRun = $lastRun ? $now->diff($lastRun)->h : 999;

    $health[] = [
        'job' => $job['job_name'],
        'status' => $job['last_run_status'],
        'last_run' => $job['last_run'],
        'hours_ago' => $hoursSinceRun,
        'ok' => $job['last_run_status'] === 'success' && $hoursSinceRun < 25,
        'error_message' => $job['error_message'],
    ];
}

v1Ok(['jobs' => $health]);

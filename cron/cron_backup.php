<?php
/**
 * cron_backup.php
 * Se debe ejecutar una vez al día mediante un cron job (ej: 0 3 * * *)
 * Requiere que "mysqldump" esté disponible en el PATH del servidor.
 */

require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/../modelos/conectar.php';

$config = Config::getInstance();
$host = escapeshellarg($config->get('DB_HOST', 'localhost'));
$user = escapeshellarg($config->get('DB_USER'));
$pass = $config->get('DB_PASS');
$db   = escapeshellarg($config->get('DB_NAME', 'aulapro'));
$con = obtenerConexion();

// Directorio de backups protegido. Vive bajo noDeploy/ (que normalmente no se
// sube a producción con esta app - solo database.sql lo hace, ver CLAUDE.md),
// así que no se puede asumir que noDeploy/.htaccess exista ahí para
// protegerlo — se escribe uno propio aquí, en el mismo sitio, por si acaso.
$backupDir = __DIR__ . '/../noDeploy/backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0700, true);
}
$backupHtaccess = $backupDir . '.htaccess';
if (!is_file($backupHtaccess)) {
    file_put_contents($backupHtaccess, "Require all denied\n");
}

// Nombre del archivo de backup
$date = date('Y-m-d_H-i-s');
$filename = $backupDir . "backup_{$date}.sql";

// Comando mysqldump — la contraseña va por la variable de entorno MYSQL_PWD,
// no por --password= en la línea de comandos: en hosting compartido, `ps aux`
// puede ser visible a otros usuarios del mismo servidor, y un argumento de
// línea de comandos queda expuesto ahí durante toda la ejecución del proceso.
$command = "mysqldump --host={$host} --user={$user} {$db} > " . escapeshellarg($filename);

echo "Iniciando copia de seguridad de la base de datos...\n";
putenv('MYSQL_PWD=' . $pass);
exec($command, $output, $returnVar);
putenv('MYSQL_PWD'); // limpiar inmediatamente tras el uso

if ($returnVar === 0) {
    echo "Copia de seguridad completada con éxito: {$filename}\n";

    // Opcional: Comprimir el archivo a ZIP para ahorrar espacio
    $zipFilename = $filename . '.zip';
    $zip = new ZipArchive();
    if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($filename, basename($filename));
        $zip->close();
        unlink($filename); // Eliminar el SQL original
        echo "Archivo comprimido a: {$zipFilename}\n";
    }

    // Opcional: Eliminar copias de seguridad de más de 30 días
    $files = glob($backupDir . '*.zip');
    $now   = time();
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 30 * 24 * 60 * 60) {
                unlink($file);
                echo "Eliminado backup antiguo: {$file}\n";
            }
        }
    }

    $status = 'success';
    $errorMsg = NULL;
} else {
    echo "Error al realizar la copia de seguridad. Código de salida: {$returnVar}\n";
    $status = 'failed';
    $errorMsg = "Exit code: {$returnVar}";
}

// Registra la ejecución del cron en la base de datos
if ($con) {
    $sql = "INSERT INTO cron_execution_log (job_name, last_run, last_run_status, error_message)
            VALUES ('cron_backup.php', NOW(), ?, ?)
            ON DUPLICATE KEY UPDATE last_run = NOW(), last_run_status = ?, error_message = ?";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssss", $status, $errorMsg, $status, $errorMsg);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

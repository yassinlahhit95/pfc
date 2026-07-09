<?php
/**
 * cron_backup.php
 * Se debe ejecutar una vez al día mediante un cron job (ej: 0 3 * * *)
 * Requiere que "mysqldump" esté disponible en el PATH del servidor.
 */

require_once __DIR__ . '/../config/Config.php';

$config = Config::getInstance();
$host = $config->get('DB_HOST', 'localhost');
$user = $config->get('DB_USER');
$pass = escapeshellarg($config->get('DB_PASS'));
$db   = escapeshellarg($config->get('DB_NAME', 'aulapro'));

// Directorio de backups protegido
$backupDir = __DIR__ . '/../noDeploy/backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0700, true);
}

// Nombre del archivo de backup
$date = date('Y-m-d_H-i-s');
$filename = $backupDir . "backup_{$date}.sql";

// Comando mysqldump
$command = "mysqldump --host={$host} --user={$user} --password={$pass} {$db} > " . escapeshellarg($filename);

echo "Iniciando copia de seguridad de la base de datos...\n";
exec($command, $output, $returnVar);

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
} else {
    echo "Error al realizar la copia de seguridad. Código de salida: {$returnVar}\n";
}

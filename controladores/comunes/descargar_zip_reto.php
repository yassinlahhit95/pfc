<?php
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";

$idReto = $_GET['id'] ?? '';

if (empty($idReto)) {
    die("ID de reto no proporcionado.");
}

$reto = obtenerRetoPorId($idReto);
$archivos = obtenerArchivosReto($idReto);

if (empty($archivos)) {
    die("Este reto no tiene archivos para descargar.");
}

$zip = new ZipArchive();
$zipName = "Materiales_Reto_" . preg_replace('/[^A-Za-z0-9]/', '_', $reto['nombreReto']) . ".zip";
$zipPath = sys_get_temp_dir() . '/' . $zipName;

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    foreach ($archivos as $arch) {
        $filePath = "../../" . $arch['rutaArchivo'];
        if (file_exists($filePath)) {
            $zip->addFile($filePath, $arch['nombreArchivo']);
        }
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-disposition: attachment; filename=' . $zipName);
    header('Content-Length: ' . filesize($zipPath));
    readfile($zipPath);
    unlink($zipPath); // Borrar el temporal
    exit;
} else {
    die("No se pudo crear el archivo ZIP.");
}
?>

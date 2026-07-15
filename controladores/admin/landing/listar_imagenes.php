<?php
// Lista los archivos ya subidos a public/uploads/landing/ para que el constructor
// pueda reutilizarlos en vez de subir un duplicado cada vez (AJAX, solo lectura).
require_once __DIR__ . '/../../../include/AdminGuard.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$soloVideo = ($_GET['tipo'] ?? '') === 'video';
$extensionesPermitidas = $soloVideo ? ['mp4'] : ['jpg', 'jpeg', 'png', 'webp'];

$uploadDir = __DIR__ . '/../../../public/uploads/landing/';
$archivos = [];

if (is_dir($uploadDir)) {
    foreach (scandir($uploadDir) as $nombre) {
        if ($nombre === '.' || $nombre === '..') continue;
        $ruta = $uploadDir . $nombre;
        if (!is_file($ruta)) continue;
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        if (!in_array($ext, $extensionesPermitidas, true)) continue;
        $archivos[] = [
            'filename' => $nombre,
            'url'      => '/public/uploads/landing/' . rawurlencode($nombre),
            'mtime'    => filemtime($ruta),
        ];
    }
}

usort($archivos, fn($a, $b) => $b['mtime'] <=> $a['mtime']);

echo json_encode(['ok' => true, 'archivos' => $archivos]);

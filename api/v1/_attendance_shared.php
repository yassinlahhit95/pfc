<?php
declare(strict_types=1);

// Shared by attendance.php and attendance-resolve.php. Underscore-prefixed
// per api/v1/.htaccess convention — blocks direct HTTP access to this file.

require_once __DIR__ . '/../../include/R2Client.php';

// Same local-file-first-then-R2-presigned-URL resolution the web app uses
// (vistas/profesores/asistencias/justificaciones.php) — just built as an
// absolute URL since the mobile client isn't rendering relative web paths.
function justificanteUrl(string $archivo): string {
    $archivoNombre = basename($archivo);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $urlLocal = "$scheme://$host/public/uploads/justificantes/$archivoNombre";
    return R2Client::documentoUrl(
        __DIR__ . '/../../public/uploads/justificantes/' . $archivoNombre,
        $urlLocal,
        'justificantes/' . $archivoNombre
    );
}

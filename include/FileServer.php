<?php
// Sirve ficheros protegidos delegando el envío de bytes al servidor web
// (LiteSpeed, vía X-Sendfile) en vez de leerlos a través de PHP con
// readfile(). El hosting compartido (Namecheap/CloudLinux) limita la cuenta
// a 20 "entry processes" simultáneos para TODO el sitio; con readfile() cada
// descarga retiene uno de esos 20 procesos durante toda la transferencia.
// Con X-Sendfile, PHP solo hace la comprobación de permisos y entrega la
// cabecera; LiteSpeed sirve el fichero directamente y libera el proceso PHP
// de inmediato.
//
// DESACTIVADO (2026-07-11): se probó en producción (server702.web-hosting.com,
// reporta "Server: LiteSpeed") y esta cuenta de hosting NO honra X-Sendfile —
// PHP entregaba la cabecera y salía sin cuerpo, así que el navegador recibía
// un PDF de 0 bytes ("Failed to load PDF document"). Puede que LSWS necesite
// una opción de vhost habilitada por el proveedor de hosting que esta cuenta
// compartida no tiene. Hasta confirmar eso (o probar X-LiteSpeed-Location en
// su lugar), se usa siempre el envío por PHP con readfile().
if (!defined('USE_XSENDFILE')) {
    define('USE_XSENDFILE', false);
}

// $r2Key: clave del objeto en Cloudflare R2 (p.ej. "pfc/abc123.pdf") — usada
// solo si el fichero YA NO está en disco local (ficheros nuevos, subidos
// después de la migración a R2; ver plan de migración, no hay backfill de
// los ficheros que ya existían en public/uploads/ antes de esto).
function servirArchivo(string $rutaAbsoluta, string $r2Key, string $nombreDescarga, string $mime, bool $inline = false): void {
    $disposition = ($inline ? 'inline' : 'attachment') . '; filename="' . rawurlencode($nombreDescarga) . '"';

    if (is_file($rutaAbsoluta)) {
        // Fichero heredado, previo a R2, todavía en disco local — se sirve
        // exactamente igual que siempre.
        header("Content-Type: $mime");
        header("Content-Disposition: $disposition");
        header("X-Content-Type-Options: nosniff");

        if (USE_XSENDFILE) {
            // LiteSpeed intercepta esta cabecera y sirve el fichero directamente;
            // no debe llamarse a readfile() ni fijar Content-Length manualmente,
            // el servidor calcula el suyo propio a partir del fichero real.
            header("X-Sendfile: $rutaAbsoluta");
            exit;
        }

        header("Content-Length: " . filesize($rutaAbsoluta));
        readfile($rutaAbsoluta);
        exit;
    }

    // No está en disco local — se asume que es un objeto de R2 (subido
    // después de la migración). Redirige a una URL firmada de corta
    // duración; R2 sirve los bytes directamente (sin coste de ancho de
    // banda en el hosting compartido, R2 no cobra egreso).
    require_once __DIR__ . '/R2Client.php';
    header('Location: ' . R2Client::presignedGetUrl($r2Key, 300, $mime, $disposition));
    exit;
}

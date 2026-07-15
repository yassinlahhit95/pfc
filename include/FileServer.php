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

function servirArchivo(string $rutaAbsoluta, string $nombreDescarga, string $mime, bool $inline = false): void {
    $disposition = $inline ? 'inline' : 'attachment';
    header("Content-Type: $mime");
    header("Content-Disposition: $disposition; filename=\"" . rawurlencode($nombreDescarga) . "\"");
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

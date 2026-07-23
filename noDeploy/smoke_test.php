<?php
// ══════════════════════════════════════════════════════════════════════
// SMOKE TEST — ejecutar antes/después de cada despliegue (manual, vía FTP)
// ══════════════════════════════════════════════════════════════════════
// No sustituye a una suite de tests real; es una red mínima para detectar
// rápido una página caída, un endpoint que devuelve 500 en vez de un JSON
// de error limpio, o un activo estático que no llegó a subirse.
//
// CLI únicamente — nunca accesible por navegador (hace peticiones salientes,
// no hay motivo para exponerlo).
//
// Uso:
//   php smoke_test.php                                  -> prueba http://pfc.test
//   php smoke_test.php https://aulapro.yassin.agency     -> prueba producción
//
// Comprobaciones autenticadas (opcionales): exporta SMOKE_TEST_USER y
// SMOKE_TEST_PASS como variables de entorno ANTES de ejecutar (nunca como
// argumento del script — quedaría en el historial de la shell). Usa una
// cuenta de prueba dedicada, no tu login de administrador personal.
//   SMOKE_TEST_USER=test@aulapro.com SMOKE_TEST_PASS=... php smoke_test.php

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Solo se puede ejecutar desde CLI.\n");
}

$baseUrl = rtrim($argv[1] ?? 'http://pfc.test', '/');

function httpGet(string $url, ?string $cookieFile = null): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS       => 5,
        CURLOPT_TIMEOUT         => 15,
        CURLOPT_HTTPHEADER      => ['X-Requested-With: XMLHttpRequest'],
    ]);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    curl_close($ch);
    return ['code' => $code, 'body' => $body ?: '', 'time' => (float)$time];
}

function httpPost(string $url, array $fields, ?string $cookieFile = null): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS       => 5,
        CURLOPT_TIMEOUT         => 15,
        CURLOPT_POST            => true,
        CURLOPT_POSTFIELDS      => http_build_query($fields),
    ]);
    if ($cookieFile) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $code, 'body' => $body ?: ''];
}

$fail = 0;
function check(string $name, bool $ok, string $detail = ''): void {
    global $fail;
    if (!$ok) $fail++;
    echo ($ok ? "  OK  " : "FALLO ") . "- $name" . ($detail !== '' ? " ($detail)" : "") . "\n";
}

// Un fallo PHP con display_errors=Off no suele mostrar el mensaje de error
// en sí, pero si algo se le escapa (entorno mal configurado, notice/warning
// con display_errors=On en un entorno concreto) esto lo atrapa igualmente.
function erroresPhpEnCuerpo(string $body): ?string {
    foreach (['Fatal error', 'Uncaught', 'Stack trace', 'ValueError', 'TypeError:', 'Parse error'] as $marcador) {
        if (stripos($body, $marcador) !== false) return $marcador;
    }
    return null;
}

echo "Smoke test contra: $baseUrl\n\n";

// ── 1. Páginas públicas ──────────────────────────────────────────────
echo "-- Páginas públicas --\n";
$paginasPublicas = [
    '/'                 => 'null',
    '/vistas/blog.php'  => 'null',
    '/vistas/login.php' => 'csrf_token',
];
foreach ($paginasPublicas as $ruta => $marcador) {
    $r   = httpGet($baseUrl . $ruta);
    $err = erroresPhpEnCuerpo($r['body']);
    $ok  = $r['code'] === 200 && !$err && ($marcador === 'null' || stripos($r['body'], $marcador) !== false);
    $detalle = $err ? "detectado '$err' en el cuerpo" : ("HTTP {$r['code']}, " . round($r['time'] * 1000) . "ms");
    check("GET $ruta", $ok, $detalle);
}

// ── 2. Endpoints AJAX sin sesión: deben rechazar limpio, nunca un 500 ──
echo "\n-- Endpoints AJAX sin sesión (deben responder 401/403 en JSON) --\n";
foreach ([
    '/controladores/comunes/contar_no_leidos.php',
    '/controladores/ajax/mensajes_polling.php',
] as $ruta) {
    $r    = httpGet($baseUrl . $ruta);
    $json = json_decode($r['body'], true);
    $ok   = in_array($r['code'], [401, 403], true) && is_array($json);
    check("GET $ruta", $ok, "HTTP {$r['code']}" . ($json === null ? ', respuesta no es JSON válido' : ''));
}

// ── 3. Activos estáticos (detecta un despliegue FTP incompleto) ───────
echo "\n-- Activos estáticos --\n";
foreach ([
    '/public/css/estilo.css',
    '/public/js/core/dashboard-shell.js',
] as $ruta) {
    $r  = httpGet($baseUrl . $ruta);
    $ok = $r['code'] === 200 && strlen($r['body']) > 100;
    check("GET $ruta", $ok, "HTTP {$r['code']}, " . strlen($r['body']) . " bytes");
}

// ── 4. Sesión autenticada (opcional) ───────────────────────────────────
$usuario = getenv('SMOKE_TEST_USER');
$clave   = getenv('SMOKE_TEST_PASS');
if ($usuario && $clave) {
    echo "\n-- Sesión autenticada --\n";
    $cookieFile = tempnam(sys_get_temp_dir(), 'smoke_');
    $paginaLogin = httpGet($baseUrl . '/vistas/login.php', $cookieFile);

    if (preg_match('/name="csrf_token" value="([^"]+)"/', $paginaLogin['body'], $m)) {
        httpPost($baseUrl . '/controladores/validacion.php', [
            'csrf_token' => $m[1], 'usuario' => $usuario, 'contrasena' => $clave, 'enviar' => 'Entrar',
        ], $cookieFile);

        $dash = httpGet($baseUrl . '/vistas/admin/inicio/dashboard.php', $cookieFile);
        $err  = erroresPhpEnCuerpo($dash['body']);
        check('Login + dashboard admin', $dash['code'] === 200 && !$err, $err ?: "HTTP {$dash['code']}");

        // Regresión directa del bug de bind_param corregido esta sesión:
        // debe devolver JSON válido autenticado, nunca un 500.
        $noLeidos = httpGet($baseUrl . '/controladores/comunes/contar_no_leidos.php', $cookieFile);
        $json     = json_decode($noLeidos['body'], true);
        check('contar_no_leidos.php autenticado', $noLeidos['code'] === 200 && is_array($json) && isset($json['count']),
              "HTTP {$noLeidos['code']}");
    } else {
        check('Login + dashboard admin', false, 'no se pudo extraer csrf_token de login.php');
    }
    @unlink($cookieFile);
} else {
    echo "\n(Comprobaciones autenticadas omitidas: define SMOKE_TEST_USER y SMOKE_TEST_PASS para incluirlas)\n";
}

// ── Resumen ─────────────────────────────────────────────────────────
echo "\n" . str_repeat('=', 50) . "\n";
if ($fail > 0) {
    echo "$fail comprobación(es) fallida(s).\n";
    exit(1);
}
echo "Todo correcto.\n";
exit(0);

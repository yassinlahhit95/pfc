<?php
// ══════════════════════════════════════════════════════════════════════
// Funciones de apoyo del asistente de instalación. Sin dependencias de
// Config.php/Security.php a propósito para los pasos 1-2 (todavía no hay
// .env que cargar) — Config.php sí se usa a partir del paso 3, una vez
// escrito el .env, en una petición HTTP nueva (Config es un singleton por
// petición, así que recoge el .env recién escrito sin problema).
// ══════════════════════════════════════════════════════════════════════

const INSTALL_LOCK_FILE = __DIR__ . '/../.installed';
const REQUIRED_EXTENSIONS = ['mysqli', 'zip', 'curl', 'openssl', 'mbstring', 'fileinfo'];
const OPTIONAL_EXTENSIONS = ['gd']; // ImageOptimizer se degrada solo si falta — no bloquea

// ── Candado ──────────────────────────────────────────────────────────
function installIsLocked(): bool {
    if (is_file(INSTALL_LOCK_FILE)) return true;

    // Segundo guardián independiente del fichero de candado: si ya hay al
    // menos un director en la base de datos, el asistente nunca debe
    // volver a ofrecer "crear la cuenta de administrador" — aunque el
    // fichero .installed se haya borrado o no se haya subido por FTP.
    $envPath = __DIR__ . '/../../.env';
    if (!is_file($envPath)) return false;

    $con = @installTryConnectFromEnv();
    if (!$con) return false;
    $res = @mysqli_query($con, "SELECT 1 FROM directores LIMIT 1");
    $tieneAdmin = $res && mysqli_num_rows($res) > 0;
    mysqli_close($con);
    return $tieneAdmin;
}

function lockInstall(): void {
    file_put_contents(INSTALL_LOCK_FILE, "Instalación completada: " . date('c') . "\n");
}

function installTryConnectFromEnv() {
    $vars = installParseEnvFile(__DIR__ . '/../../.env');
    if (!$vars || empty($vars['DB_HOST']) || empty($vars['DB_USER']) || empty($vars['DB_NAME'])) return false;
    return @mysqli_connect($vars['DB_HOST'], $vars['DB_USER'], $vars['DB_PASS'] ?? '', $vars['DB_NAME']);
}

function installParseEnvFile(string $path): ?array {
    if (!is_file($path)) return null;
    $out = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;
        [$k, $v] = explode('=', $line, 2);
        $out[trim($k)] = trim($v);
    }
    return $out;
}

// ── Paso 1: entorno ──────────────────────────────────────────────────
function checkEnvironment(): array {
    $checks = [];

    $checks['php'] = [
        'label' => 'PHP 8.3 o superior',
        'ok'    => version_compare(PHP_VERSION, '8.3.0', '>='),
        'detail'=> 'Versión detectada: ' . PHP_VERSION,
        'bloqueante' => true,
    ];

    foreach (REQUIRED_EXTENSIONS as $ext) {
        $checks['ext_' . $ext] = [
            'label' => "Extensión PHP: $ext",
            'ok'    => extension_loaded($ext),
            'detail'=> extension_loaded($ext) ? 'Cargada' : 'No encontrada — obligatoria',
            'bloqueante' => true,
        ];
    }
    foreach (OPTIONAL_EXTENSIONS as $ext) {
        $checks['ext_' . $ext] = [
            'label' => "Extensión PHP: $ext (opcional)",
            'ok'    => extension_loaded($ext),
            'detail'=> extension_loaded($ext) ? 'Cargada' : 'No encontrada — la optimización de imágenes se desactivará sola',
            'bloqueante' => false,
        ];
    }

    $writables = [
        'public/uploads' => __DIR__ . '/../../public/uploads',
        'logs'           => __DIR__ . '/../../logs',
    ];
    foreach ($writables as $label => $path) {
        $existe = is_dir($path);
        $escribible = $existe && is_writable($path);
        $checks['dir_' . $label] = [
            'label' => "Carpeta escribible: $label/",
            'ok'    => $escribible,
            'detail'=> !$existe ? 'La carpeta no existe' : ($escribible ? 'Escribible' : 'Existe pero no tiene permisos de escritura'),
            'bloqueante' => true,
        ];
    }

    $checks['env_writable'] = [
        'label' => 'Se puede crear el fichero .env',
        'ok'    => is_writable(__DIR__ . '/../..'),
        'detail'=> is_writable(__DIR__ . '/../..') ? 'La raíz del proyecto es escribible' : 'Sin permiso de escritura en la raíz del proyecto',
        'bloqueante' => true,
    ];

    return $checks;
}

function environmentPasses(array $checks): bool {
    foreach ($checks as $c) {
        if ($c['bloqueante'] && !$c['ok']) return false;
    }
    return true;
}

// ── Paso 2: base de datos ────────────────────────────────────────────
function testDbConnection(string $host, string $user, string $pass, string $db): array {
    mysqli_report(MYSQLI_REPORT_OFF);
    $con = @mysqli_connect($host, $user, $pass);
    if (!$con) {
        return ['ok' => false, 'msg' => 'No se pudo conectar: ' . mysqli_connect_error()];
    }
    $existia = @mysqli_select_db($con, $db);
    if (!$existia) {
        // La base de datos puede no existir todavía — se intenta crear.
        if (!@mysqli_query($con, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            $err = mysqli_error($con);
            mysqli_close($con);
            return ['ok' => false, 'msg' => "La base de datos '$db' no existe y no se pudo crear: $err"];
        }
        mysqli_select_db($con, $db);
    }
    mysqli_close($con);
    return ['ok' => true, 'msg' => 'Conexión correcta.'];
}

// Aplica noDeploy/database.sql completo con mysqli_multi_query — el fichero
// no contiene DELIMITER/TRIGGER/PROCEDURE (verificado), así que una sola
// llamada multi-query es suficiente, sin necesidad de trocear por ';'.
function runSchemaImport(string $host, string $user, string $pass, string $db): array {
    $sqlPath = __DIR__ . '/../../noDeploy/database.sql';
    if (!is_file($sqlPath)) {
        return ['ok' => false, 'msg' => 'No se encontró noDeploy/database.sql.'];
    }
    $sql = file_get_contents($sqlPath);

    mysqli_report(MYSQLI_REPORT_OFF);
    $con = @mysqli_connect($host, $user, $pass, $db);
    if (!$con) {
        return ['ok' => false, 'msg' => 'No se pudo conectar para importar el esquema: ' . mysqli_connect_error()];
    }
    mysqli_set_charset($con, 'utf8mb4');

    // Si ya existen tablas (reintento tras un fallo a mitad, o BD reutilizada),
    // no se aborta — database.sql usa DROP TABLE IF EXISTS antes de cada
    // CREATE TABLE, así que reimportar es seguro.
    if (!mysqli_multi_query($con, $sql)) {
        $err = mysqli_error($con);
        mysqli_close($con);
        return ['ok' => false, 'msg' => "Error al importar el esquema: $err"];
    }
    // Hay que agotar todos los resultsets (CREATE/INSERT los generan) antes
    // de poder volver a usar la conexión con seguridad.
    do {
        if ($res = mysqli_store_result($con)) mysqli_free_result($res);
        if (mysqli_errno($con)) {
            $err = mysqli_error($con);
            mysqli_close($con);
            return ['ok' => false, 'msg' => "Error durante la importación: $err"];
        }
    } while (mysqli_more_results($con) && mysqli_next_result($con));

    mysqli_close($con);
    return ['ok' => true, 'msg' => 'Esquema importado correctamente.'];
}

// ── Escritura del .env ───────────────────────────────────────────────
function writeEnvFile(array $db): array {
    $envPath = __DIR__ . '/../../.env';
    $examplePath = __DIR__ . '/../../.env.example';

    if (!is_file($examplePath)) {
        return ['ok' => false, 'msg' => 'No se encontró .env.example como plantilla.'];
    }

    $contenido = file_get_contents($examplePath);
    $reemplazos = [
        'DB_HOST=localhost'        => 'DB_HOST=' . $db['host'],
        'DB_USER=your_db_user'     => 'DB_USER=' . $db['user'],
        'DB_PASS=your_db_password' => 'DB_PASS=' . $db['pass'],
        'DB_NAME=your_db_name'     => 'DB_NAME=' . $db['name'],
        'BOLETIN_SECRET=your_boletin_secret'         => 'BOLETIN_SECRET=' . bin2hex(random_bytes(32)),
        'PII_ENCRYPTION_KEY=your_pii_encryption_key' => 'PII_ENCRYPTION_KEY=' . bin2hex(random_bytes(32)),
        'APP_ENV=production'  => 'APP_ENV=' . ($db['app_env'] ?? 'production'),
        'APP_URL=https://aulapro.yassin.agency' => 'APP_URL=' . ($db['app_url'] ?? ''),
    ];
    $contenido = strtr($contenido, $reemplazos);
    $contenido = "# Generado por el asistente de instalación — " . date('c') . "\n" . $contenido;

    if (file_put_contents($envPath, $contenido) === false) {
        return ['ok' => false, 'msg' => 'No se pudo escribir el fichero .env.'];
    }
    @chmod($envPath, 0640);
    return ['ok' => true, 'msg' => '.env generado correctamente.'];
}

// ── Sincroniza el origen CORS de api/.htaccess con APP_URL ─────────────
// api/.htaccess hardcodea el dominio permitido para Access-Control-Allow-Origin
// (ver el comentario en ese fichero: es la única fuente de verdad de CORS,
// a propósito — duplicarlo en PHP mandaba dos cabeceras conflictivas). Las
// apps móviles nativas ignoran CORS, así que esto no afecta al asistente ni
// a la app — pero sin esto, un futuro cliente web/PWA en el dominio real del
// centro quedaría bloqueado por seguir apuntando al dominio de ejemplo.
// Mejor esfuerzo: si falla o si $appUrl viene vacío (campo opcional del
// paso 2), no bloquea el resto de la instalación.
function updateCorsOrigin(string $appUrl): void {
    if ($appUrl === '') return;
    $path = __DIR__ . '/../../api/.htaccess';
    if (!is_file($path) || !is_writable($path)) return;
    $contenido = file_get_contents($path);
    if ($contenido === false) return;
    $nuevo = preg_replace(
        '/Access-Control-Allow-Origin "[^"]*"/',
        'Access-Control-Allow-Origin "' . rtrim($appUrl, '/') . '"',
        $contenido,
        1
    );
    if ($nuevo !== null && $nuevo !== $contenido) {
        file_put_contents($path, $nuevo);
    }
}

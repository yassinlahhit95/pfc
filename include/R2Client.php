<?php
require_once __DIR__ . '/../config/Config.php';
require_once __DIR__ . '/CircuitBreaker.php';
require_once __DIR__ . '/Logger.php';

// ══════════════════════════════════════════════════════════════════════
// CLOUDFLARE R2 — almacenamiento de ficheros subidos (API S3-compatible,
// capa gratuita). Firma AWS Signature V4 manual, sin SDK — sigue el mismo
// patrón curl que el resto del proyecto para llamadas a APIs externas
// (ver controladores/comunes/email_helper.php, integración con Brevo).
//
// putObject/deleteObject firman con cabecera Authorization (petición
// normal). presignedGetUrl firma por query-string (sin cuerpo, GET
// diferido) — es un ensamblado distinto, no la misma firma con otro verbo.
// publicUrl no firma nada: requiere Public Access habilitado en el bucket
// y solo debe usarse para contenido público (imágenes de landing/blog),
// nunca para documentos protegidos por permisos.
// ══════════════════════════════════════════════════════════════════════
final class R2Client {

    // Lee y valida la configuración necesaria para firmar/llamar a R2.
    // Falla alto (RuntimeException) en vez de firmar con credenciales
    // vacías — un fallo silencioso aquí sería peor que un error visible,
    // ya que este código solo se ejecuta cuando de verdad hace falta R2
    // (los ficheros locales existentes siguen sirviéndose sin tocar esto).
    private static function config(): array {
        $cfg = Config::getInstance();
        $accountId = $cfg->get('R2_ACCOUNT_ID', '');
        $accessKey = $cfg->get('R2_ACCESS_KEY_ID', '');
        $secretKey = $cfg->get('R2_SECRET_ACCESS_KEY', '');
        $bucket    = $cfg->get('R2_BUCKET_NAME', '');
        if ($accountId === '' || $accessKey === '' || $secretKey === '' || $bucket === '') {
            throw new RuntimeException(
                'Cloudflare R2 no está configurado (faltan R2_ACCOUNT_ID / R2_ACCESS_KEY_ID / ' .
                'R2_SECRET_ACCESS_KEY / R2_BUCKET_NAME en .env).'
            );
        }
        return [$accountId, $accessKey, $secretKey, $bucket];
    }

    private static function host(string $accountId): string {
        return "{$accountId}.r2.cloudflarestorage.com";
    }

    // Codifica cada segmento de la clave por separado — la barra `/` entre
    // segmentos NUNCA se codifica (a diferencia de dentro de un valor de
    // query string, donde sí debe ir como %2F). Confundir esto es el error
    // más común al construir el "canonical URI" a mano.
    private static function encodeKeyPath(string $key): string {
        return implode('/', array_map('rawurlencode', explode('/', $key)));
    }

    // Derivación de la clave de firma: HMAC-SHA256 anidado, cada paso
    // alimenta al siguiente como CLAVE (no como mensaje) en bytes crudos
    // (4º argumento `true` de hash_hmac) — pasar hex en vez de bytes crudos
    // en cualquier paso intermedio es el bug más habitual al implementar
    // SigV4 a mano y produce una firma que nunca valida.
    private static function signingKey(string $secretKey, string $dateStamp): string {
        $kSecret  = 'AWS4' . $secretKey;
        $kDate    = hash_hmac('sha256', $dateStamp, $kSecret, true);
        $kRegion  = hash_hmac('sha256', 'auto', $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        return hash_hmac('sha256', 'aws4_request', $kService, true);
    }

    // ── Petición firmada con cabecera Authorization — PUT / DELETE ──────
    private static function request(string $method, string $key, string $body, string $contentType = ''): array {
        [$accountId, $accessKey, $secretKey, $bucket] = self::config();
        $host      = self::host($accountId);
        $amzDate   = gmdate('Ymd\THis\Z'); // SigV4 es siempre UTC — gmdate(), nunca date()
        $dateStamp = gmdate('Ymd');
        $payloadHash = hash('sha256', $body);

        $canonicalUri         = '/' . $bucket . '/' . self::encodeKeyPath($key);
        $canonicalQueryString = '';

        $headersToSign = [
            'host'                 => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date'           => $amzDate,
        ];
        ksort($headersToSign);
        $canonicalHeaders = '';
        foreach ($headersToSign as $k => $v) { $canonicalHeaders .= "$k:$v\n"; }
        $signedHeaders = implode(';', array_keys($headersToSign));

        $canonicalRequest = implode("\n", [
            $method, $canonicalUri, $canonicalQueryString, $canonicalHeaders, $signedHeaders, $payloadHash,
        ]);

        $credentialScope = "{$dateStamp}/auto/s3/aws4_request";
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256', $amzDate, $credentialScope, hash('sha256', $canonicalRequest),
        ]);

        $signature = hash_hmac('sha256', $stringToSign, self::signingKey($secretKey, $dateStamp));
        $authorization = "AWS4-HMAC-SHA256 Credential={$accessKey}/{$credentialScope}, " .
                          "SignedHeaders={$signedHeaders}, Signature={$signature}";

        if (CircuitBreaker::isOpen('r2')) {
            Logger::error('R2 circuit OPEN — solicitud omitida', ['key' => $key, 'method' => $method]);
            return ['status' => 0, 'body' => '', 'error' => 'circuit_open'];
        }

        $headers = [
            'Host: ' . $host,
            'x-amz-date: ' . $amzDate,
            'x-amz-content-sha256: ' . $payloadHash,
            'Authorization: ' . $authorization,
        ];
        if ($contentType !== '') $headers[] = 'Content-Type: ' . $contentType;

        $h = curl_init();
        curl_setopt($h, CURLOPT_URL, "https://{$host}{$canonicalUri}");
        curl_setopt($h, CURLOPT_CUSTOMREQUEST, $method);
        if ($method === 'PUT') curl_setopt($h, CURLOPT_POSTFIELDS, $body);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_TIMEOUT, 30);
        curl_setopt($h, CURLOPT_HTTPHEADER, $headers);

        $res  = curl_exec($h);
        $code = (int)curl_getinfo($h, CURLINFO_HTTP_CODE);
        $err  = curl_error($h);
        curl_close($h);

        if ($err || $code >= 500) {
            Logger::error('Fallo en solicitud R2: ' . ($err ?: "HTTP $code"), ['key' => $key, 'method' => $method]);
            CircuitBreaker::recordFailure('r2');
        } else {
            CircuitBreaker::recordSuccess('r2');
        }

        return ['status' => $code, 'body' => (string)$res, 'error' => $err];
    }

    private static function getTenantKey(string $key): string {
        $prefix = Config::getInstance()->get('R2_TENANT_PREFIX', '');
        if ($prefix !== '' && $prefix !== 'default') {
            return $prefix . '/' . ltrim($key, '/');
        }
        return ltrim($key, '/');
    }

    // APCu es un único espacio de memoria compartida por pool de PHP-FPM — si dos
    // tenants llegan a compartir pool, las claves sin prefijo filtran la cuota/uso
    // de almacenamiento de un tenant hacia la aplicación de límites de otro. Se añade
    // a cada clave de caché de sincronización SaaS el mismo prefijo de tenant que ya
    // usan las claves de objeto de R2.
    private static function tenantCacheKey(string $key): string {
        return $key . ':' . Config::getInstance()->get('R2_TENANT_PREFIX', 'default');
    }

    public static function putObject(string $key, string $bytes, string $contentType): bool {
        $key = self::getTenantKey($key);
        if (function_exists('apcu_fetch')) {
            $limitKey = self::tenantCacheKey('saas_max_storage_gb');
            $usedKey  = self::tenantCacheKey('saas_used_storage_bytes');
            $limitGb  = apcu_fetch($limitKey);
            if ($limitGb !== false && $limitGb > 0) {
                $usedBytes = apcu_fetch($usedKey);
                if ($usedBytes === false) {
                    $usage = self::totalUsage();
                    $usedBytes = $usage['bytes'];
                    apcu_store($usedKey, $usedBytes, 3600);
                }

                $limitBytes = $limitGb * 1024 * 1024 * 1024;
                if (($usedBytes + strlen($bytes)) > $limitBytes) {
                    throw new RuntimeException("Límite de almacenamiento SaaS ({$limitGb} GB) excedido. Por favor, contacte a su administrador.");
                }
            }
        }

        $res = self::request('PUT', $key, $bytes, $contentType);

        if ($res['status'] === 200 && function_exists('apcu_inc')) {
            $usedKey = self::tenantCacheKey('saas_used_storage_bytes');
            if (apcu_exists($usedKey)) {
                apcu_inc($usedKey, strlen($bytes));
            }
        }

        return $res['status'] === 200;
    }

    public static function deleteObject(string $key): bool {
        $key = self::getTenantKey($key);
        $res = self::request('DELETE', $key, '');
        // 404 = ya no existe en R2 (o nunca existió ahí, p.ej. un fichero
        // que solo vivía en disco local antes de esta migración) — no es
        // un fallo real dado que no hay migración retroactiva de ficheros
        // ya existentes; se trata como éxito (mismo criterio best-effort).
        return $res['status'] === 204 || $res['status'] === 404;
    }

    // ── ListObjectsV2 — GET al bucket (no a una clave), firma por cabecera
    // Authorization igual que put/delete, pero con query string propia (el
    // ensamblado de request() asume canonicalQueryString vacía, así que esto
    // no reutiliza ese método: aquí la query string SÍ entra en la firma).
    // Página única (1000 objetos máx) + estado de paginación — extraído de
    // listObjects() para que totalUsage() pueda recorrer el bucket entero sin
    // duplicar la firma SigV4, sin cambiar el contrato de listObjects() para
    // quien ya lo usa (listado de una carpeta de la landing, nunca supera 1000).
    private static function listObjectsPage(string $prefix, ?string $continuationToken = null): array {
        $prefix = self::getTenantKey($prefix);
        [$accountId, $accessKey, $secretKey, $bucket] = self::config();
        $host      = self::host($accountId);
        $amzDate   = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');
        $payloadHash = hash('sha256', '');

        $queryParams = ['list-type' => '2', 'prefix' => $prefix, 'max-keys' => '1000'];
        if ($continuationToken !== null) $queryParams['continuation-token'] = $continuationToken;
        ksort($queryParams, SORT_STRING);
        $canonicalQueryParts = [];
        foreach ($queryParams as $k => $v) {
            $canonicalQueryParts[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        $canonicalQueryString = implode('&', $canonicalQueryParts);

        $canonicalUri = '/' . $bucket;
        $headersToSign = [
            'host'                 => $host,
            'x-amz-content-sha256' => $payloadHash,
            'x-amz-date'           => $amzDate,
        ];
        ksort($headersToSign);
        $canonicalHeaders = '';
        foreach ($headersToSign as $k => $v) { $canonicalHeaders .= "$k:$v\n"; }
        $signedHeaders = implode(';', array_keys($headersToSign));

        $canonicalRequest = implode("\n", [
            'GET', $canonicalUri, $canonicalQueryString, $canonicalHeaders, $signedHeaders, $payloadHash,
        ]);

        $credentialScope = "{$dateStamp}/auto/s3/aws4_request";
        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256', $amzDate, $credentialScope, hash('sha256', $canonicalRequest),
        ]);
        $signature = hash_hmac('sha256', $stringToSign, self::signingKey($secretKey, $dateStamp));
        $authorization = "AWS4-HMAC-SHA256 Credential={$accessKey}/{$credentialScope}, " .
                          "SignedHeaders={$signedHeaders}, Signature={$signature}";

        if (CircuitBreaker::isOpen('r2')) {
            Logger::error('R2 circuit OPEN — listado omitido', ['prefix' => $prefix]);
            return ['objects' => [], 'isTruncated' => false, 'nextToken' => null];
        }

        $headers = [
            'Host: ' . $host,
            'x-amz-date: ' . $amzDate,
            'x-amz-content-sha256: ' . $payloadHash,
            'Authorization: ' . $authorization,
        ];

        $h = curl_init();
        curl_setopt($h, CURLOPT_URL, "https://{$host}{$canonicalUri}?{$canonicalQueryString}");
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_TIMEOUT, 30);
        curl_setopt($h, CURLOPT_HTTPHEADER, $headers);
        $res  = curl_exec($h);
        $code = (int)curl_getinfo($h, CURLINFO_HTTP_CODE);
        $err  = curl_error($h);
        curl_close($h);

        if ($err || $code >= 500) {
            Logger::error('Fallo en listado R2: ' . ($err ?: "HTTP $code"), ['prefix' => $prefix]);
            CircuitBreaker::recordFailure('r2');
            return ['objects' => [], 'isTruncated' => false, 'nextToken' => null];
        }
        CircuitBreaker::recordSuccess('r2');

        if ($code !== 200 || $res === false) return ['objects' => [], 'isTruncated' => false, 'nextToken' => null];

        $xml = @simplexml_load_string($res);
        if ($xml === false) return ['objects' => [], 'isTruncated' => false, 'nextToken' => null];

        $objetos = [];
        foreach ($xml->Contents as $item) {
            $objetos[] = [
                'key'          => (string)$item->Key,
                'size'         => (int)$item->Size,
                'lastModified' => (string)$item->LastModified,
            ];
        }
        $isTruncated = ((string)$xml->IsTruncated) === 'true';
        $nextToken   = $isTruncated && isset($xml->NextContinuationToken) ? (string)$xml->NextContinuationToken : null;

        return ['objects' => $objetos, 'isTruncated' => $isTruncated, 'nextToken' => $nextToken];
    }

    public static function listObjects(string $prefix): array {
        return self::listObjectsPage($prefix)['objects'];
    }

    // Recorre el bucket entero (todas las páginas) sumando tamaño y contando
    // objetos — usado por el widget de almacenamiento del panel admin. Tope
    // de 50 páginas (50.000 objetos) como salvaguarda ante un bucle sin fin;
    // muy por encima de lo que este proyecto generará en la práctica.
    public static function totalUsage(string $prefix = ''): array {
        $bytes = 0;
        $objectCount = 0;
        $token = null;
        $pages = 0;
        do {
            $page = self::listObjectsPage($prefix, $token);
            foreach ($page['objects'] as $obj) {
                $bytes += $obj['size'];
                $objectCount++;
            }
            $token = $page['nextToken'];
            $pages++;
        } while ($page['isTruncated'] && $token !== null && $pages < 50);

        return ['bytes' => $bytes, 'objectCount' => $objectCount];
    }

    // ── URL pre-firmada (firma por query-string) — GET ───────────────────
    // Ensamblado distinto al de request(): sin cuerpo (payload "UNSIGNED-
    // PAYLOAD"), la fecha viaja en la query string en vez de en una
    // cabecera, y la propia firma se añade a la URL en vez de mandarse en
    // un header Authorization.
    public static function presignedGetUrl(
        string $key,
        int $expirySeconds,
        ?string $responseContentType = null,
        ?string $responseContentDisposition = null
    ): string {
        $key = self::getTenantKey($key);
        [$accountId, $accessKey, $secretKey, $bucket] = self::config();
        $host      = self::host($accountId);
        $amzDate   = gmdate('Ymd\THis\Z');
        $dateStamp = gmdate('Ymd');
        $credentialScope = "{$dateStamp}/auto/s3/aws4_request";

        $params = [
            'X-Amz-Algorithm'     => 'AWS4-HMAC-SHA256',
            'X-Amz-Credential'    => $accessKey . '/' . $credentialScope,
            'X-Amz-Date'          => $amzDate,
            'X-Amz-Expires'       => (string)$expirySeconds,
            'X-Amz-SignedHeaders' => 'host',
        ];
        if ($responseContentType !== null)        $params['response-content-type']        = $responseContentType;
        if ($responseContentDisposition !== null) $params['response-content-disposition'] = $responseContentDisposition;

        // Todos los parámetros anteriores deben entrar en la query string
        // canónica que se firma ANTES de calcular X-Amz-Signature — si se
        // añaden después de firmar, R2 rechaza la firma porque no coincide
        // con lo que realmente llega en la petición.
        ksort($params, SORT_STRING);
        $canonicalQueryParts = [];
        foreach ($params as $k => $v) {
            $canonicalQueryParts[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        $canonicalQueryString = implode('&', $canonicalQueryParts);

        $canonicalUri     = '/' . $bucket . '/' . self::encodeKeyPath($key);
        $canonicalHeaders = "host:{$host}\n";
        $signedHeaders    = 'host';
        $payloadHash      = 'UNSIGNED-PAYLOAD'; // estándar para GET pre-firmadas: no hay cuerpo que hashear

        $canonicalRequest = implode("\n", [
            'GET', $canonicalUri, $canonicalQueryString, $canonicalHeaders, $signedHeaders, $payloadHash,
        ]);

        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256', $amzDate, $credentialScope, hash('sha256', $canonicalRequest),
        ]);

        $signature = hash_hmac('sha256', $stringToSign, self::signingKey($secretKey, $dateStamp));

        // X-Amz-Signature es el ÚNICO parámetro que no formó parte de lo
        // firmado (es la salida, no una entrada) — se añade el último.
        return "https://{$host}{$canonicalUri}?{$canonicalQueryString}&X-Amz-Signature={$signature}";
    }

    // URL pública permanente — requiere Public Access habilitado en el
    // bucket (R2_PUBLIC_URL en .env). Sin firma, sin expiración. Solo para
    // contenido genuinamente público (imágenes de landing/blog) — nunca
    // para documentos que hoy pasan por un control de permisos en PHP.
    public static function publicUrl(string $key): string {
        $key = self::getTenantKey($key);
        $base = Config::getInstance()->get('R2_PUBLIC_URL', '');
        if ($base === '') {
            throw new RuntimeException('R2_PUBLIC_URL no está configurado en .env.');
        }
        return $base . '/' . self::encodeKeyPath($key);
    }

    // Igual que servirArchivo() en concepto (local heredado primero, R2
    // después) pero para imágenes públicas embebidas en vistas — sin firma,
    // ya que aquí no hay control de acceso ni riesgo de expiración. Si el
    // nombre está vacío, devuelve '' (nada que mostrar).
    public static function imagenUrl(string $rutaLocalAbsoluta, string $urlLocal, string $r2Key): string {
        if ($urlLocal === '') return '';
        if (is_file($rutaLocalAbsoluta)) return $urlLocal;
        try {
            return self::publicUrl($r2Key);
        } catch (Throwable $t) {
            error_log("R2Client Error (imagenUrl): " . $t->getMessage());
            return '';
        }
    }

    // Descarga los bytes de un objeto público (p.ej. para incrustarlo como
    // Data URI en un PDF generado en el servidor, donde hace falta el
    // contenido real y no una URL). Solo para contenido ya servido vía
    // publicUrl() — nunca para documentos protegidos. Devuelve '' si falla.
    public static function fetchPublicBytes(string $r2Key): string {
        $url = self::publicUrl($r2Key);
        $h = curl_init();
        curl_setopt($h, CURLOPT_URL, $url);
        curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($h, CURLOPT_TIMEOUT, 10);
        $body = curl_exec($h);
        $code = (int)curl_getinfo($h, CURLINFO_HTTP_CODE);
        curl_close($h);
        return ($body !== false && $code === 200) ? $body : '';
    }

    // Para documentos semi-sensibles enlazados directamente desde una vista
    // ya protegida por su propio Guard de rol (justificantes de gastos,
    // justificantes de falta) — no hay un controlador de descarga dedicado
    // como verTFG.php, así que aquí se genera la URL firmada en el momento
    // de renderizar, en vez de redirigir desde un controlador aparte. Si el
    // nombre está vacío, devuelve '' (nada que enlazar).
    public static function documentoUrl(string $rutaLocalAbsoluta, string $urlLocal, string $r2Key, int $expirySeconds = 300): string {
        if ($urlLocal === '') return '';
        if (is_file($rutaLocalAbsoluta)) return $urlLocal;
        try {
            return self::presignedGetUrl($r2Key, $expirySeconds);
        } catch (Throwable $t) {
            error_log("R2Client Error (documentoUrl): " . $t->getMessage());
            return $urlLocal;
        }
    }
}

<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/directores.php";
require_once __DIR__ . "/../../modelos/conectar.php";
require_once __DIR__ . "/../../modelos/secretarias.php";
require_once __DIR__ . "/../../include/CircuitBreaker.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN JWT (OAuth 2.0 para FCM v1)
// ══════════════════════════════════════════════════════════════════════
function obtenerAccessToken() {
    static $accessTokenCache = null;
    if ($accessTokenCache !== null) return $accessTokenCache;

    $rutaConfig = __DIR__ . '/../../config/service-account.json';

    if (!file_exists($rutaConfig)) {
        return null;
    }

    $contenido = file_get_contents($rutaConfig);
    if (!$contenido) return null;

    $datosJson = json_decode($contenido, true);
    if (!$datosJson || !isset($datosJson['client_email']) || !isset($datosJson['private_key'])) {
        error_log("Firebase Error: service-account.json tiene un formato inválido.");
        return null;
    }

    $email        = $datosJson['client_email'];
    $clavePrivada = $datosJson['private_key'];

    $cabecera       = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $momentoActual  = time();
    $cuerpoCarga    = json_encode([
        'iss'   => $email,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $momentoActual + 3600,
        'iat'   => $momentoActual
    ]);

    $base64UrlHeader  = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($cabecera));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($cuerpoCarga));

    $firma = '';
    if (!openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $firma, $clavePrivada, OPENSSL_ALGO_SHA256)) {
        error_log("Firebase Error: No se pudo firmar el JWT. Revisa la clave privada.");
        return null;
    }

    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($firma));
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => 'https://oauth2.googleapis.com/token',
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt
        ])
    ]);

    $resultado       = curl_exec($ch);
    $datosRespuesta  = json_decode($resultado, true);
    curl_close($ch);

    $accessTokenCache = $datosRespuesta['access_token'] ?? null;
    return $accessTokenCache;
}

// ══════════════════════════════════════════════════════════════════════
// ENVÍO DE NOTIFICACIONES PUSH
// ══════════════════════════════════════════════════════════════════════
function enviarNotificacionFirebase($token, $titulo, $mensaje, string $tipo = 'chat_message', array $extra = []) {
    if (empty($token)) return false;

    // Circuit breaker: no machacar FCM/Google OAuth cuando están devolviendo errores.
    if (CircuitBreaker::isOpen('fcm')) {
        error_log("FCM circuit OPEN — push skipped for token " . substr($token, 0, 20));
        return false;
    }

    $config     = Config::getInstance();
    $idProyecto = $config->get('FIREBASE_PROJECT_ID', 'pfc1-5c23c');
    $urlFCM     = "https://fcm.googleapis.com/v1/projects/$idProyecto/messages:send";

    $accessToken = obtenerAccessToken();
    if (!$accessToken) {
        error_log("FCM Error: No se pudo obtener el Access Token. Revisa service-account.json");
        CircuitBreaker::recordFailure('fcm');
        return false;
    }

    $urlLogo = "https://yassin.agency/public/imagenes/aulapro.png";

    // El payload `data` de FCM solo acepta valores string — convertir cada
    // entrada de $extra (puede llevar enteros como idModulo/idTarea) antes de fusionarlas.
    $dataPayload = array_merge(
        ['title' => $titulo, 'body' => $mensaje, 'type' => $tipo],
        array_map('strval', $extra)
    );

    $cuerpoCarga = [
        'message' => [
            'token'        => $token,
            'notification' => [
                'title' => $titulo,
                'body'  => $mensaje,
                'image' => $urlLogo
            ],
            'data' => $dataPayload,
            'android' => [
                'priority'     => 'high',
                'notification' => ['channel_id' => 'aulapro_default']
            ],
            'webpush' => [
                'notification' => [
                    'icon'  => $urlLogo,
                    'badge' => $urlLogo
                ]
            ]
        ]
    ];

    $datosJson = json_encode($cuerpoCarga);
    $cabeceras = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $urlFCM,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => $cabeceras,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_POSTFIELDS     => $datosJson
    ]);

    $resultadoEnvio = curl_exec($ch);
    $codigoHttp     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errCurl        = curl_error($ch);
    curl_close($ch);

    if ($errCurl) {
        error_log("FCM CURL Error: " . $errCurl);
        CircuitBreaker::recordFailure('fcm');
        return false;
    }

    if ($codigoHttp !== 200) {
        error_log("FCM API Error (HTTP $codigoHttp): " . $resultadoEnvio);
        CircuitBreaker::recordFailure('fcm');
    } else {
        error_log("FCM Success: Notificación enviada correctamente al token: " . substr($token, 0, 20) . "...");
        CircuitBreaker::recordSuccess('fcm');
    }

    return $resultadoEnvio;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════
function obtenerTokenUsuario($idUsuario, $rolUsuario) {
    switch ($rolUsuario) {
        case 'estudiante':
            return obtenerTokenFCMEstudiante($idUsuario);
        case 'profesor':
            return obtenerTokenFCMProfesor($idUsuario);
        case 'admin':
        case 'director':
            return obtenerTokenFCMDirector($idUsuario);
        case 'tutor':
            return obtenerTokenFCM('tutores', 'idTutor', $idUsuario);
        case 'secretaria':
            return obtenerTokenFCMSecretaria($idUsuario);
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
// ENVÍO EN PARALELO MULTI-CURL (NO BLOQUEANTE)
// ══════════════════════════════════════════════════════════════════════

/**
 * Envía la misma notificación push en paralelo a una lista de tokens.
 */
function enviarNotificacionesFirebaseSimultaneas(array $tokens, $titulo, $mensaje, string $tipo = 'chat_message', array $extra = []) {
    $tokens = array_filter(array_unique($tokens));
    if (empty($tokens)) return false;

    if (CircuitBreaker::isOpen('fcm')) {
        error_log("FCM circuit OPEN — batch push skipped for " . count($tokens) . " tokens.");
        return false;
    }

    $config     = Config::getInstance();
    $idProyecto = $config->get('FIREBASE_PROJECT_ID', 'pfc1-5c23c');
    $urlFCM     = "https://fcm.googleapis.com/v1/projects/$idProyecto/messages:send";

    $accessToken = obtenerAccessToken();
    if (!$accessToken) {
        error_log("FCM Error: No se pudo obtener el Access Token en envío masivo.");
        CircuitBreaker::recordFailure('fcm');
        return false;
    }

    $urlLogo = "https://yassin.agency/public/imagenes/aulapro.png";
    $dataPayload = array_merge(
        ['title' => $titulo, 'body' => $mensaje, 'type' => $tipo],
        array_map('strval', $extra)
    );

    $cabeceras = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $mh = curl_multi_init();
    $handles = [];

    foreach ($tokens as $token) {
        $cuerpoCarga = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $titulo,
                    'body'  => $mensaje,
                    'image' => $urlLogo
                ],
                'data' => $dataPayload,
                'android' => [
                    'priority'     => 'high',
                    'notification' => ['channel_id' => 'aulapro_default']
                ],
                'webpush' => [
                    'notification' => [
                        'icon'  => $urlLogo,
                        'badge' => $urlLogo
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $urlFCM,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $cabeceras,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_POSTFIELDS     => json_encode($cuerpoCarga),
            CURLOPT_TIMEOUT        => 4 // Límite de 4 segundos por petición para evitar cuelgues
        ]);

        curl_multi_add_handle($mh, $ch);
        $handles[$token] = $ch;
    }

    $active = null;
    do {
        $status = curl_multi_exec($mh, $active);
        if ($active) {
            curl_multi_select($mh, 0.05);
        }
    } while ($active && $status == CURLM_OK);

    $exitos = 0; $fallos = 0;
    foreach ($handles as $token => $ch) {
        $resultadoEnvio = curl_multi_getcontent($ch);
        $codigoHttp     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCurl        = curl_error($ch);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        if ($errCurl || $codigoHttp !== 200) {
            $fallos++;
            error_log("FCM parallel error: " . ($errCurl ?: "HTTP $codigoHttp") . " - Token: " . substr($token, 0, 15) . "...");
        } else {
            $exitos++;
        }
    }
    curl_multi_close($mh);

    if ($fallos > 0 && $exitos === 0) {
        CircuitBreaker::recordFailure('fcm');
    } else {
        CircuitBreaker::recordSuccess('fcm');
    }

    error_log("FCM Batch push completed: $exitos exitosos, $fallos fallidos.");
    return $exitos > 0;
}

/**
 * Envía múltiples notificaciones personalizadas en paralelo.
 * Cada envío en la lista debe ser: ['token' => '...', 'titulo' => '...', 'mensaje' => '...', 'tipo' => '...', 'extra' => [...]]
 */
function enviarNotificacionesFirebasePersonalizadasSimultaneas(array $envios) {
    if (empty($envios)) return false;

    if (CircuitBreaker::isOpen('fcm')) {
        error_log("FCM circuit OPEN — custom batch push skipped.");
        return false;
    }

    $accessToken = obtenerAccessToken();
    if (!$accessToken) {
        error_log("FCM Error: No se pudo obtener el Access Token en masivo personalizado.");
        CircuitBreaker::recordFailure('fcm');
        return false;
    }

    $config     = Config::getInstance();
    $idProyecto = $config->get('FIREBASE_PROJECT_ID', 'pfc1-5c23c');
    $urlFCM     = "https://fcm.googleapis.com/v1/projects/$idProyecto/messages:send";
    $urlLogo    = "https://yassin.agency/public/imagenes/aulapro.png";

    $cabeceras = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $mh = curl_multi_init();
    $handles = [];

    foreach ($envios as $envio) {
        $token = $envio['token'] ?? '';
        if (empty($token)) continue;

        $titulo  = $envio['titulo'] ?? '';
        $mensaje = $envio['mensaje'] ?? '';
        $tipo    = $envio['tipo'] ?? 'message';
        $extra   = $envio['extra'] ?? [];

        $dataPayload = array_merge(
            ['title' => $titulo, 'body' => $mensaje, 'type' => $tipo],
            array_map('strval', $extra)
        );

        $cuerpoCarga = [
            'message' => [
                'token'        => $token,
                'notification' => [
                    'title' => $titulo,
                    'body'  => $mensaje,
                    'image' => $urlLogo
                ],
                'data' => $dataPayload,
                'android' => [
                    'priority'     => 'high',
                    'notification' => ['channel_id' => 'aulapro_default']
                ],
                'webpush' => [
                    'notification' => [
                        'icon'  => $urlLogo,
                        'badge' => $urlLogo
                    ]
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $urlFCM,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => $cabeceras,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_POSTFIELDS     => json_encode($cuerpoCarga),
            CURLOPT_TIMEOUT        => 4
        ]);

        curl_multi_add_handle($mh, $ch);
        $handles[] = ['ch' => $ch, 'token' => $token];
    }

    if (empty($handles)) {
        curl_multi_close($mh);
        return false;
    }

    $active = null;
    do {
        $status = curl_multi_exec($mh, $active);
        if ($active) {
            curl_multi_select($mh, 0.05);
        }
    } while ($active && $status == CURLM_OK);

    $exitos = 0; $fallos = 0;
    foreach ($handles as $h) {
        $ch    = $h['ch'];
        $token = $h['token'];

        $resultadoEnvio = curl_multi_getcontent($ch);
        $codigoHttp     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errCurl        = curl_error($ch);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        if ($errCurl || $codigoHttp !== 200) {
            $fallos++;
            error_log("FCM custom parallel error: " . ($errCurl ?: "HTTP $codigoHttp") . " - Token: " . substr($token, 0, 15) . "...");
        } else {
            $exitos++;
        }
    }
    curl_multi_close($mh);

    if ($fallos > 0 && $exitos === 0) {
        CircuitBreaker::recordFailure('fcm');
    } else {
        CircuitBreaker::recordSuccess('fcm');
    }

    error_log("FCM Custom batch push completed: $exitos exitosos, $fallos fallidos.");
    return $exitos > 0;
}


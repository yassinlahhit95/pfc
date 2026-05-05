<?php
/**
 * Helper para la gestión de notificaciones push a través de Firebase Cloud Messaging (FCM) V1.
 */

require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/directores.php";

/**
 * Obtiene el Access Token de Google para autenticar las peticiones de FCM.
 * @return string|null Token de acceso o null si no se puede obtener (falta config).
 */
function obtenerAccessToken() {
    // Definimos la ruta del archivo de credenciales de forma relativa al proyecto
    $rutaConfig = __DIR__ . '/../../config/service-account.json';
    
    if (!file_exists($rutaConfig)) {
        // Si no existe el archivo, retornamos null silenciosamente.
        return null;
    }

    $contenido = file_get_contents($rutaConfig);
    if (!$contenido) return null;

    $datosJson = json_decode($contenido, true);
    if (!$datosJson || !isset($datosJson['client_email']) || !isset($datosJson['private_key'])) {
        error_log("Firebase Error: service-account.json tiene un formato inválido.");
        return null;
    }

    $email = $datosJson['client_email'];
    $clavePrivada = $datosJson['private_key'];

    $cabecera = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $momentoActual = time();
    $cuerpoCarga = json_encode([
        'iss' => $email,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $momentoActual + 3600,
        'iat' => $momentoActual
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($cabecera));
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
        CURLOPT_URL => 'https://oauth2.googleapis.com/token',
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ])
    ]);

    $resultado = curl_exec($ch);
    $datosRespuesta = json_decode($resultado, true);
    curl_close($ch);

    return $datosRespuesta['access_token'] ?? null;
}

/**
 * Envía una notificación push a un dispositivo específico.
 * @param string $token Token FCM del dispositivo destino.
 * @param string $titulo Título de la notificación.
 * @param string $mensaje Cuerpo de la notificación.
 * @return string|false Respuesta de Google o false si falló la configuración inicial.
 */
function enviarNotificacionFirebase($token, $titulo, $mensaje) {
    if (empty($token)) return false;

    $idProyecto = "pfc1-5c23c"; // ID del proyecto en Firebase Console
    $urlFCM = "https://fcm.googleapis.com/v1/projects/$idProyecto/messages:send";
    
    $accessToken = obtenerAccessToken();
    if (!$accessToken) {
        // Si no hay configuración, salimos sin hacer nada (evita errores en cadena).
        return false;
    }

    $cuerpoCarga = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $titulo,
                'body' => $mensaje
            ],
            'data' => [
                'title' => $titulo,
                'body' => $mensaje,
                'type' => 'chat_message'
            ],
            'webpush' => [
                'notification' => [
                    'icon' => '/pfc/public/img/logoSuperAdmin.png'
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
        CURLOPT_URL => $urlFCM,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $cabeceras,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $datosJson
    ]);

    $resultadoEnvio = curl_exec($ch);
    $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($codigoHttp !== 200) {
        error_log("FCM Error ($codigoHttp): " . $resultadoEnvio);
    }
    
    return $resultadoEnvio;
}

/**
 * Obtiene el token FCM de un usuario desde los modelos.
 */
function obtenerTokenUsuario($idUsuario, $rolUsuario) {
    switch ($rolUsuario) {
        case 'estudiante': 
            return obtenerTokenFCMEstudiante($idUsuario);
        case 'profesor': 
            return obtenerTokenFCMProfesor($idUsuario);
        case 'admin': 
            return obtenerTokenFCMDirector($idUsuario);
    }
    return null;
}

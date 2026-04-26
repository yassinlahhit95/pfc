<?php
/**
 * Helper para la gestión de notificaciones push a través de Firebase Cloud Messaging (FCM) V1.
 */

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

    $json = json_decode($contenido, true);
    if (!$json || !isset($json['client_email']) || !isset($json['private_key'])) {
        error_log("Firebase Error: service-account.json tiene un formato inválido.");
        return null;
    }

    $email = $json['client_email'];
    $clavePrivada = $json['private_key'];

    $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
    $ahora = time();
    $payload = json_encode([
        'iss' => $email,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => $ahora + 3600,
        'iat' => $ahora
    ]);

    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

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
    $datos = json_decode($resultado, true);
    curl_close($ch);

    return $datos['access_token'] ?? null;
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

    $projectId = "pfc1-5c23c"; // ID del proyecto en Firebase Console
    $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";
    
    $accessToken = obtenerAccessToken();
    if (!$accessToken) {
        // Si no hay configuración, salimos sin hacer nada (evita errores en cadena).
        return false;
    }

    $payload = [
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

    $json = json_encode($payload);
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $json
    ]);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("FCM Error ($httpCode): " . $result);
        // No guardamos el error en SESSION aquí para no ensuciar la UI general
        // si falla un envío puntual de red.
    }
    
    return $result;
}

/**
 * Obtiene el token FCM de un usuario desde la base de datos.
 */
function obtenerTokenUsuario($userId, $userRole) {
    require_once __DIR__ . "/../../modelos/conectar.php";
    $conexion = obtenerConexion();
    
    $tabla = "";
    $columnaId = "";

    switch ($userRole) {
        case 'estudiante': $tabla = "estudiantes"; $columnaId = "idEstudiante"; break;
        case 'profesor': $tabla = "profesores"; $columnaId = "idProfesor"; break;
        case 'admin': $tabla = "directores"; $columnaId = "idDirector"; break;
    }

    if ($tabla != "") {
        $stmt = mysqli_prepare($conexion, "SELECT fcm_token FROM $tabla WHERE $columnaId = ?");
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        
        if ($fila = mysqli_fetch_assoc($resultado)) {
            $token = $fila['fcm_token'];
            mysqli_close($conexion);
            return $token;
        }
    }
    mysqli_close($conexion);
    return null;
}


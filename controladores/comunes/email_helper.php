<?php
/**
 * Función para enviar correos electrónicos usando la API REST de Brevo (Sendinblue)
 */
function sendEmail($to, $subject, $htmlContent) {
    // CARGAR CLAVE DE API DESDE CONFIGURACIÓN SEGURA
    // Nota: Crea un archivo config/secrets.php con: <?php $brevo_api_key = 'TU_CLAVE_AQUÍ';
    $pathSecrets = __DIR__ . '/../../config/secrets.php';
    if (file_exists($pathSecrets)) {
        include $pathSecrets;
        $apiKey = $brevo_api_key ?? '';
    } else {
        // Fallback para desarrollo o si no se ha configurado
        $apiKey = 'TU_CLAVE_API_DEBE_IR_EN_CONFIG_SECRETS_PHP';
    }
    
    if (empty($apiKey) || $apiKey === 'TU_CLAVE_API_DEBE_IR_EN_CONFIG_SECRETS_PHP') {
        error_log("Error: Brevo API Key no configurada en $pathSecrets");
        return false;
    }
    
    $url = 'https://api.brevo.com/v3/smtp/email';
    
    $data = [
        'sender' => ['name' => 'Sistema Académico PFC', 'email' => 'yassin.lahhit@gmail.com'],
        'to' => [['email' => $to]],
        'subject' => $subject,
        'htmlContent' => $htmlContent
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 201 || $httpCode === 200;
}

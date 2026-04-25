<?php
/**
 * Función para enviar correos electrónicos usando la API REST de Brevo (Sendinblue)
 */
function sendEmail($to, $subject, $htmlContent) {
    // Reemplaza con tu clave de API de Brevo
    $apiKey = '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';
    
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
?>

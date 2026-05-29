<?php
function sendEmail($to, $subject, $htmlContent, $senderName = 'CFP - AulaPro | Sistema Académico') {
    require_once __DIR__ . '/../../config/Config.php';

    $config = Config::getInstance();
    $key = $config->get('BREVO_API_KEY');

    if (empty($key)) {
        error_log("ERROR: Falta la API Key de Brevo. Verifica tu archivo .env");
        $_SESSION['ultimo_error_email'] = "API Key de Brevo no configurada";
        return false;
    }

    $url = 'https://api.brevo.com/v3/smtp/email';

    $payload = [
        'sender' => ['name' => $senderName, 'email' => 'notas@yassin.agency'],
        'to' => [['email' => $to]],
        'subject' => $subject,
        'htmlContent' => $htmlContent
    ];

    $h = curl_init();
    curl_setopt($h, CURLOPT_URL, $url);
    curl_setopt($h, CURLOPT_POST, true);
    curl_setopt($h, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($h, CURLOPT_HTTPHEADER, [
        'api-key: ' . $key,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $res = curl_exec($h);
    $code = curl_getinfo($h, CURLINFO_HTTP_CODE);
    $e = curl_error($h);
    curl_close($h);

    if ($e) {
        error_log("Error Brevo CURL: " . $e);
        $_SESSION['ultimo_error_email'] = "Error de conexión: " . $e;
    }

    if ($code !== 200 && $code !== 201) {
        error_log("Brevo HTTP $code: " . $res);
        $_SESSION['ultimo_error_email'] = "Error Brevo API";
    }

    return $code === 201 || $code === 200;
}
?>

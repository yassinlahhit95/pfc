<?php
require_once __DIR__ . '/../../include/CircuitBreaker.php';

// ══════════════════════════════════════════════════════════════════════
// ENVÍO DE CORREO ELECTRÓNICO VÍA BREVO API
// ══════════════════════════════════════════════════════════════════════
function sendEmail($to, $subject, $htmlContent, $senderName = null) {
    if ($senderName === null) {
        require_once __DIR__ . '/../../include/FeatureGuard.php';
        $senderName = 'CFP - ' . FeatureGuard::getCenterName() . ' | Sistema Académico';
    }
    require_once __DIR__ . '/../../config/Config.php';
    require_once __DIR__ . '/../../include/Logger.php';

    // Circuit breaker: dejar de machacar Brevo cuando está caído.
    if (CircuitBreaker::isOpen('brevo')) {
        Logger::error("Brevo circuit OPEN — email skipped", ['to' => $to]);
        $_SESSION['ultimo_error_email'] = 'Servicio de correo temporalmente no disponible (circuit open)';
        return false;
    }

    $config = Config::getInstance();
    $key = $config->get('BREVO_API_KEY');

    if (empty($key)) {
        Logger::error("Falta la API Key de Brevo. Verifica tu archivo .env");
        $_SESSION['ultimo_error_email'] = "API Key de Brevo no configurada";
        return false;
    }

    $senderEmail = $config->get('BREVO_SENDER_EMAIL', 'notas@yassin.agency');
    if (empty($senderEmail)) {
        $senderEmail = 'notas@yassin.agency';
    }

    $url = 'https://api.brevo.com/v3/smtp/email';

    $payload = [
        'sender' => ['name' => $senderName, 'email' => $senderEmail],
        'to' => [['email' => $to]],
        'subject' => $subject,
        'htmlContent' => $htmlContent
    ];

    $h = curl_init();
    curl_setopt($h, CURLOPT_URL, $url);
    curl_setopt($h, CURLOPT_POST, true);
    curl_setopt($h, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($h, CURLOPT_TIMEOUT, 10);
    curl_setopt($h, CURLOPT_HTTPHEADER, [
        'api-key: ' . $key,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $res  = curl_exec($h);
    $code = curl_getinfo($h, CURLINFO_HTTP_CODE);
    $e    = curl_error($h);
    curl_close($h);

    if ($e) {
        Logger::error("Error Brevo CURL: " . $e, ['to' => $to]);
        $_SESSION['ultimo_error_email'] = "Error de conexión: " . $e;
        CircuitBreaker::recordFailure('brevo');
        return false;
    }

    if ($code !== 200 && $code !== 201) {
        Logger::error("Brevo HTTP $code: " . $res, ['to' => $to]);
        $_SESSION['ultimo_error_email'] = "Error Brevo API (HTTP $code)";
        CircuitBreaker::recordFailure('brevo');
        return false;
    }

    CircuitBreaker::recordSuccess('brevo');
    return true;
}


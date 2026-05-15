<?php
function sendEmail($to, $subject, $htmlContent) {
    $pathSecrets = __DIR__ . '/../../config/secrets.php';
    $apiKey = '';

    if (file_exists($pathSecrets)) {
        include $pathSecrets;
        $apiKey = $brevo_api_key ?? '';
    }

    if (empty($apiKey)) {
        error_log("Error de Correo: API Key de Brevo no encontrada en $pathSecrets. Asegúrate de haber creado el archivo y definido la variable $brevo_api_key.");
        return false;
    }

    $url = 'https://api.brevo.com/v3/smtp/email';

    $data = [
        'sender' => ['name' => 'CFP - AulaPro | Notas finales ', 'email' => 'notas@yassin.agency'],
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
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("Error de CURL en Brevo: " . $err);
        $_SESSION['ultimo_error_email'] = "Error de conexión (CURL): " . $err;
    }

    if ($httpCode !== 200 && $httpCode !== 201) {
        $msgError = "Brevo HTTP $httpCode: " . $response;
        error_log($msgError);
        $_SESSION['ultimo_error_email'] = $msgError;
    }

    return $httpCode === 201 || $httpCode === 200;
}
?>

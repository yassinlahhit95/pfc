<?php
// DELETE THIS FILE AFTER TESTING
require_once __DIR__ . '/config/Config.php';

$config = Config::getInstance();
$key = $config->get('BREVO_API_KEY');

echo "<pre>";
echo "API Key loaded: " . (empty($key) ? "❌ EMPTY" : "✅ " . substr($key, 0, 12) . "...") . "\n";
echo "cURL enabled:   " . (function_exists('curl_init') ? "✅ yes" : "❌ no") . "\n\n";

if (empty($key) || !function_exists('curl_init')) {
    echo "Fix the above issues first.\n</pre>";
    exit;
}

// Send a test email
$payload = [
    'sender'      => ['name' => 'AulaPro Test', 'email' => 'notas@yassin.agency'],
    'to'          => [['email' => 'yassin.lahhit@gmail.com']],
    'subject'     => 'Test Brevo desde local',
    'htmlContent' => '<p>Si recibes esto, Brevo funciona correctamente desde local.</p>',
];

$h = curl_init('https://api.brevo.com/v3/smtp/email');
curl_setopt($h, CURLOPT_POST, true);
curl_setopt($h, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
curl_setopt($h, CURLOPT_HTTPHEADER, [
    'api-key: ' . $key,
    'Content-Type: application/json',
    'Accept: application/json',
]);

$res  = curl_exec($h);
$code = curl_getinfo($h, CURLINFO_HTTP_CODE);
$err  = curl_error($h);
curl_close($h);

echo "HTTP status: $code\n";
if ($err)  echo "cURL error:  $err\n";
echo "Response:    $res\n";

if ($code === 201) {
    echo "\n✅ Email enviado. Revisa tu bandeja (y spam).\n";
} else {
    echo "\n❌ Fallo. Lee la respuesta de arriba para ver el motivo.\n";
}
echo "</pre>";

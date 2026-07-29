<?php
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/Security.php';
// ══════════════════════════════════════════════════════════════════════
// GENERACIÓN DE CREDENCIALES
// ══════════════════════════════════════════════════════════════════════

// Genera credenciales temporales seguras para una cuenta recién creada.
// Devuelve [hash, plain]: hash se guarda en BD; plain se envía al usuario por email.
// Sustituye la antigua contraseña fija '123456'.
function generarCredencialesTemporales(string $email, string $nombre, string $rolLabel): array {
    $plain = Security::generateTempPassword(14);
    $hash  = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

    if (session_status() === PHP_SESSION_NONE) @session_start();
    $_SESSION['credenciales_generadas'] = [
        'email'    => $email,
        'password' => $plain,
        'rol'      => $rolLabel,
    ];

    // El email nunca bloquea la creación de la cuenta (p. ej. en local sin Brevo)
    $helper = __DIR__ . '/../controladores/comunes/email_helper.php';
    if (is_file($helper)) {
        require_once $helper;
        if (function_exists('sendEmail') && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $asunto = 'Tus credenciales de acceso - AulaPro';
            $html = "<h2>Hola " . htmlspecialchars($nombre) . ",</h2>
                <p>Se ha creado tu cuenta de <strong>" . htmlspecialchars($rolLabel) . "</strong> en AulaPro.</p>
                <div style='background:#f3f4f6;padding:16px;border-radius:8px;margin:16px 0'>
                    <p><strong>Usuario:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Contraseña temporal:</strong> " . htmlspecialchars($plain) . "</p>
                </div>
                <p><small>Por seguridad, cambia tu contraseña la primera vez que accedas.</small></p>";
            @sendEmail($email, $asunto, $html);
        }
    }

    return [$hash, $plain];
}

// ══════════════════════════════════════════════════════════════════════
// MENSAJES
// ══════════════════════════════════════════════════════════════════════

// Añade la contraseña temporal generada al mensaje de éxito (solo una vez).
// Consume $_SESSION['credenciales_generadas'] para no volver a mostrarla.
function mensajeExitoConCredenciales(string $baseMsg): string {
    if (session_status() === PHP_SESSION_NONE) @session_start();
    $cred = $_SESSION['credenciales_generadas'] ?? null;
    unset($_SESSION['credenciales_generadas']);
    if ($cred && !empty($cred['password'])) {
        $baseMsg .= " Contraseña temporal: " . $cred['password']
                  . " — enviada por email. Anótala: no se volverá a mostrar.";
    }
    return $baseMsg;
}

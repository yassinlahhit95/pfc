<?php
require_once __DIR__ . '/Security.php';

/**
 * Genera credenciales temporales SEGURAS para una cuenta recién creada.
 *
 * Devuelve [hash, plain]:
 *   - el hash se guarda en la base de datos,
 *   - el plain se envía al usuario por email y se expone UNA vez al admin
 *     que crea la cuenta (vía $_SESSION) para que pueda comunicarlo.
 *
 * Sustituye la antigua contraseña fija '123456' (credencial por defecto débil).
 */
function generarCredencialesTemporales(string $email, string $nombre, string $rolLabel): array {
    $plain = Security::generateTempPassword(14);
    $hash  = password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);

    if (session_status() === PHP_SESSION_NONE) @session_start();
    $_SESSION['credenciales_generadas'] = [
        'email'    => $email,
        'password' => $plain,
        'rol'      => $rolLabel,
    ];

    // Envío por email — nunca bloquea la creación de la cuenta (p. ej. en local sin Brevo)
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

/**
 * Añade (una sola vez) la contraseña temporal recién generada a un mensaje de
 * éxito, para que el admin que crea la cuenta pueda comunicársela al usuario.
 * Consume $_SESSION['credenciales_generadas'].
 */
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

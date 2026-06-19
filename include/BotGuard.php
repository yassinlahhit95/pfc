<?php
class BotGuard {
    // Tiempo mínimo (segundos) que tarda un humano en rellenar un formulario
    const MIN_FORM_TIME = 3;

    public static function renderFields(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['form_loaded_at'] = time();
        // El campo honeypot tiene un nombre plausible; los bots lo rellenan, los humanos no lo ven
        return '<input type="text" name="website" value="" autocomplete="off" tabindex="-1" style="display:none;position:absolute;left:-9999px;" aria-hidden="true">';
    }

    public static function validate(): bool {
        if (!empty($_POST['website'])) {
            return false;
        }
        if (isset($_SESSION['form_loaded_at'])) {
            $elapsed = time() - (int)$_SESSION['form_loaded_at'];
            if ($elapsed < self::MIN_FORM_TIME) {
                return false;
            }
        }
        return true;
    }
}

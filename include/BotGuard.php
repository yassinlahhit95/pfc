<?php
class BotGuard {
    // Minimum seconds a human takes to fill a form
    const MIN_FORM_TIME = 3;

    public static function renderFields(): string {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['form_loaded_at'] = time();
        // The honeypot field has a plausible name; bots fill it, humans don't see it
        return '<input type="text" name="website" value="" autocomplete="off" tabindex="-1" style="display:none;position:absolute;left:-9999px;" aria-hidden="true">';
    }

    public static function validate(): bool {
        // Reject if honeypot field is not empty
        if (!empty($_POST['website'])) {
            return false;
        }
        // Reject if form was submitted suspiciously fast
        if (isset($_SESSION['form_loaded_at'])) {
            $elapsed = time() - (int)$_SESSION['form_loaded_at'];
            if ($elapsed < self::MIN_FORM_TIME) {
                return false;
            }
        }
        return true;
    }
}

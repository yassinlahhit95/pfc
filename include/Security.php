<?php
/**
 * Clase de Seguridad - Manejo de CSRF, Rate Limiting, Validaciones
 */

class Security {
    const CSRF_TOKEN_LENGTH = 32;
    const CSRF_VALIDITY_SECONDS = 3600; // 1 hora
    const RATE_LIMIT_ATTEMPTS = 5;
    const RATE_LIMIT_WINDOW = 300; // 5 minutos

    /**
     * Inicializa sesión de seguridad
     */
    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerar ID de sesión periódicamente
        if (!isset($_SESSION['_last_regen']) || time() - $_SESSION['_last_regen'] > 600) {
            session_regenerate_id(true);
            $_SESSION['_last_regen'] = time();
        }
    }

    /**
     * Genera token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida token CSRF
     */
    public static function validateCSRFToken($token = null) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        $token = $token ?? ($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '');

        // Verificar token
        if ($token !== $_SESSION['csrf_token']) {
            return false;
        }

        // Verificar tiempo de expiración
        if (time() - $_SESSION['csrf_token_time'] > self::CSRF_VALIDITY_SECONDS) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }

        return true;
    }

    /**
     * Rate limiting para login
     * Previene ataques de fuerza bruta
     */
    public static function checkRateLimit($email, $maxAttempts = self::RATE_LIMIT_ATTEMPTS) {
        // Usar base de datos si disponible, sino usar sesión
        $attemptKey = 'rate_limit_' . md5($email);

        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'blocked_until' => null
            ];
        }

        $attempt = &$_SESSION[$attemptKey];
        $now = time();

        // Si está bloqueado, verificar si ya pasó el tiempo
        if ($attempt['blocked_until'] !== null && $now < $attempt['blocked_until']) {
            $remainingTime = $attempt['blocked_until'] - $now;
            return [
                'allowed' => false,
                'message' => "Demasiados intentos. Intenta de nuevo en $remainingTime segundos.",
                'remaining_time' => $remainingTime
            ];
        }

        // Limpiar intentos si pasó la ventana
        if ($now - $attempt['first_attempt'] > self::RATE_LIMIT_WINDOW) {
            $attempt['attempts'] = 0;
            $attempt['first_attempt'] = $now;
            $attempt['blocked_until'] = null;
        }

        return [
            'allowed' => true,
            'attempts' => $attempt['attempts'],
            'max_attempts' => $maxAttempts
        ];
    }

    /**
     * Registra intento de login fallido
     */
    public static function recordFailedLogin($email) {
        $attemptKey = 'rate_limit_' . md5($email);

        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = [
                'attempts' => 0,
                'first_attempt' => time(),
                'blocked_until' => null
            ];
        }

        $attempt = &$_SESSION[$attemptKey];
        $attempt['attempts']++;

        // Bloquear después de X intentos
        if ($attempt['attempts'] >= self::RATE_LIMIT_ATTEMPTS) {
            $attempt['blocked_until'] = time() + self::RATE_LIMIT_WINDOW;
            return ['blocked' => true, 'until' => $attempt['blocked_until']];
        }

        return ['blocked' => false];
    }

    /**
     * Limpia intentos de login después de login exitoso
     */
    public static function clearFailedLogins($email) {
        $attemptKey = 'rate_limit_' . md5($email);
        unset($_SESSION[$attemptKey]);
    }

    /**
     * Sanitiza entrada - remove tags and trim
     */
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return trim(strip_tags($input));
    }

    /**
     * Valida email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida contraseña (mínimo 8 caracteres, mayúscula, minúscula, número)
     */
    public static function validatePassword($password) {
        if (strlen($password) < 8) {
            return ['valid' => false, 'error' => 'La contraseña debe tener al menos 8 caracteres'];
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return ['valid' => false, 'error' => 'La contraseña debe contener al menos una mayúscula'];
        }

        if (!preg_match('/[a-z]/', $password)) {
            return ['valid' => false, 'error' => 'La contraseña debe contener al menos una minúscula'];
        }

        if (!preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'error' => 'La contraseña debe contener al menos un número'];
        }

        return ['valid' => true];
    }

    /**
     * Hash de contraseña seguro
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verifica contraseña
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Necesita rehashing (cost puede cambiar)
     */
    public static function passwordNeedsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Escapa para SQL (aunque se deba usar prepared statements)
     */
    public static function escapeSql($value) {
        if (is_array($value)) {
            return array_map([self::class, 'escapeSql'], $value);
        }
        if (is_numeric($value)) {
            return $value;
        }
        return "'" . str_replace("'", "''", $value) . "'";
    }

    /**
     * Escapa para HTML
     */
    public static function escapeHtml($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validar DNI español
     */
    public static function validateDNI($dni) {
        $dni = strtoupper(trim($dni));

        if (!preg_match('/^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/', $dni)) {
            return false;
        }

        $validLetters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $dniNumber = substr($dni, 0, 8);
        $letter = substr($dni, 8, 1);
        $expectedLetter = $validLetters[$dniNumber % 23];

        return $letter === $expectedLetter;
    }

    /**
     * Valida URL
     */
    public static function validateURL($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valida Telefono (España: 9 dígitos, empieza por 6, 7, 8 o 9)
     */
    public static function validatePhone($phone) {
        $phone = str_replace([' ', '-', '.'], '', $phone);
        return preg_match('/^[6789][0-9]{8}$/', $phone) === 1;
    }

    /**
     * Valida CIF español básico
     */
    public static function validateCIF($cif) {
        $cif = strtoupper(trim($cif));
        return preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/', $cif) === 1;
    }
}

// Inicializar sesión segura
Security::initSession();
?>

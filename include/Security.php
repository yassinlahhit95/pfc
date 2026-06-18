<?php
/**
 * Clase de Seguridad - Manejo de CSRF, Rate Limiting, Validaciones
 */

class Security {
    const CSRF_TOKEN_LENGTH = 32;
    const CSRF_VALIDITY_SECONDS = 3600; // 1 hora
    const RATE_LIMIT_ATTEMPTS = 5;
    const RATE_LIMIT_WINDOW = 300; // 5 minutos

    public static function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Configurar cookie antes de iniciar la sesión.
            // El flag Secure solo se activa sobre HTTPS para no romper el entorno de desarrollo local.
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

            @ini_set('session.use_strict_mode', '1');  // rechaza SIDs externos (previene session fixation)
            @ini_set('session.use_only_cookies', '1');
            @ini_set('session.cookie_httponly', '1');
            if ($secure) @ini_set('session.cookie_secure', '1');

            if (PHP_VERSION_ID >= 70300) {
                @session_set_cookie_params([
                    'lifetime' => 0,
                    'path'     => '/',
                    'httponly' => true,
                    'secure'   => $secure,
                    'samesite' => 'Lax',
                ]);
            } else {
                @session_set_cookie_params(0, '/; samesite=Lax', '', $secure, true);
            }
            session_start();
        }
        self::enforceSessionSecurity();
    }

    /**
     * Defensa de sesión para usuarios autenticados:
     * - Fingerprint de User-Agent (mitiga secuestro de sesión)
     * - Caducidad por inactividad (idle timeout)
     * No se aplica a visitantes anónimos para no romper el token CSRF del login.
     */
    public static function enforceSessionSecurity() {
        $authKeys = ['idAdmin', 'idProfesor', 'idEstudiante', 'idTutor'];
        $isAuth = false;
        foreach ($authKeys as $k) {
            if (!empty($_SESSION[$k])) { $isAuth = true; break; }
        }
        if (!$isAuth) return;

        // Fingerprint estable (User-Agent). No usamos IP para no expulsar a usuarios móviles.
        $fp = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
        if (!isset($_SESSION['_fp'])) {
            $_SESSION['_fp'] = $fp;
        } elseif (!hash_equals($_SESSION['_fp'], $fp)) {
            self::destroySession();
            return;
        }

        // Invalidación tras cambio de contraseña (revalida como máximo cada 60s).
        // Si pwd_changed_at en BD es posterior al de esta sesión → sesión obsoleta.
        $roleMap = [
            'idAdmin'      => ['directores',  'idDirector'],
            'idProfesor'   => ['profesores',  'idProfesor'],
            'idEstudiante' => ['estudiantes', 'idEstudiante'],
            'idTutor'      => ['tutores',     'idTutor'],
        ];
        if ((time() - (int)($_SESSION['_pwd_check'] ?? 0)) >= 60) {
            $_SESSION['_pwd_check'] = time();
            // Security.php suele incluirse antes que conectar.php → cargarlo bajo demanda
            $conectar = __DIR__ . '/../modelos/conectar.php';
            if (is_file($conectar)) require_once $conectar;
            foreach (($roleMap ?? []) as $sk => $info) {
                if (!function_exists('obtenerConexion')) break;
                [$tabla, $idCol] = $info;
                if (empty($_SESSION[$sk])) continue;
                try {
                    $con = obtenerConexion();
                    $stmt = mysqli_prepare($con, "SELECT pwd_changed_at FROM `$tabla` WHERE `$idCol` = ?");
                    if ($stmt) {
                        $idv = (int)$_SESSION[$sk];
                        mysqli_stmt_bind_param($stmt, "i", $idv);
                        mysqli_stmt_execute($stmt);
                        $r = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
                        $dbAt = (!empty($r['pwd_changed_at'])) ? strtotime($r['pwd_changed_at']) : 0;
                        if ($dbAt > 0 && $dbAt > (int)($_SESSION['_pwd_at'] ?? 0)) {
                            self::destroySession();
                            return;
                        }
                    }
                } catch (\Throwable $e) { /* columna ausente: ignorar */ }
                break;
            }
        }

        // Idle timeout (segundos). Configurable vía SESSION_TIMEOUT en .env.
        $timeout = 3600;
        if (class_exists('Config')) {
            $cfg = Config::getInstance();
            $timeout = max(300, (int)$cfg->getInteger('SESSION_TIMEOUT', 3600));
        }
        $now = time();
        if (isset($_SESSION['_last_activity']) && ($now - $_SESSION['_last_activity']) > $timeout) {
            self::destroySession();
            return;
        }
        $_SESSION['_last_activity'] = $now;

        // Cambio de contraseña obligatorio en el primer acceso.
        // Solo redirigimos navegaciones a /vistas/ (los controladores los bloquean
        // sus propios Guards). Exime la propia página de cambio para no crear bucles.
        if (!empty($_SESSION['must_change_password'])) {
            $script = $_SERVER['SCRIPT_NAME'] ?? '';
            $esVista        = strpos($script, '/vistas/') !== false;
            $esPaginaCambio = strpos($script, 'cambiar_password') !== false;
            if ($esVista && !$esPaginaCambio) {
                $base = '';
                foreach (['/vistas/', '/controladores/'] as $seg) {
                    $pos = strpos($script, $seg);
                    if ($pos !== false) { $base = substr($script, 0, $pos); break; }
                }
                header('Location: ' . $base . '/vistas/cambiar_password.php');
                exit;
            }
        }

    }

    public static function destroySession() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'] ?? '/', $p['domain'] ?? '', !empty($p['secure']), true);
        }
        @session_destroy();
    }

    public static function generateTempPassword($length = 14) {
        $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower = 'abcdefghijkmnpqrstuvwxyz';
        $dig   = '23456789';
        $sym   = '!@#$%-_';
        $all   = $upper . $lower . $dig . $sym;
        $pwd  = $upper[random_int(0, strlen($upper) - 1)];
        $pwd .= $lower[random_int(0, strlen($lower) - 1)];
        $pwd .= $dig[random_int(0, strlen($dig) - 1)];
        $pwd .= $sym[random_int(0, strlen($sym) - 1)];
        for ($i = strlen($pwd); $i < $length; $i++) {
            $pwd .= $all[random_int(0, strlen($all) - 1)];
        }
        return str_shuffle($pwd);
    }

    // Solo invocar en login/logout: la regeneración periódica destruye el token CSRF
    // en hosting compartido (LiteSpeed/cPanel) incluso con el flag keep-old-data activo.
    public static function regenerateSession() {
        $keep = ['csrf_token', 'csrf_token_time'];
        $saved = [];
        foreach ($keep as $k) {
            if (isset($_SESSION[$k])) $saved[$k] = $_SESSION[$k];
        }
        session_regenerate_id(true);
        foreach ($saved as $k => $v) {
            $_SESSION[$k] = $v;
        }
    }

    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token = null) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }

        $token = $token ?? ($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '');

        // Comparación en tiempo constante (evita timing side-channel)
        if (!is_string($token) || $token === '' || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }

        if (!isset($_SESSION['csrf_token_time'])
            || time() - $_SESSION['csrf_token_time'] > self::CSRF_VALIDITY_SECONDS) {
            unset($_SESSION['csrf_token']);
            unset($_SESSION['csrf_token_time']);
            return false;
        }

        return true;
    }

    /** Rate limiting de login por sesión (por email). Complementa el límite de IP de validacion.php. */
    public static function checkRateLimit($email, $maxAttempts = self::RATE_LIMIT_ATTEMPTS) {
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

        if ($attempt['blocked_until'] !== null && $now < $attempt['blocked_until']) {
            $remainingTime = $attempt['blocked_until'] - $now;
            return [
                'allowed' => false,
                'message' => "Demasiados intentos. Intenta de nuevo en $remainingTime segundos.",
                'remaining_time' => $remainingTime
            ];
        }

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

        if ($attempt['attempts'] >= self::RATE_LIMIT_ATTEMPTS) {
            $attempt['blocked_until'] = time() + self::RATE_LIMIT_WINDOW;
            return ['blocked' => true, 'until' => $attempt['blocked_until']];
        }

        return ['blocked' => false];
    }

    public static function clearFailedLogins($email) {
        $attemptKey = 'rate_limit_' . md5($email);
        unset($_SESSION[$attemptKey]);
    }

    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return trim(strip_tags($input));
    }

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

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Rehasha (a coste 12) la contraseña tras un login correcto si el hash
     * almacenado usa un coste/algoritmo más débil. La tabla/columna se validan
     * contra una lista blanca → seguro frente a inyección.
     */
    public static function rehashOnLogin($con, string $tabla, string $idCol, $idVal, string $password, string $hashActual): void {
        $allow = [
            'directores'  => 'idDirector',
            'profesores'  => 'idProfesor',
            'estudiantes' => 'idEstudiante',
            'tutores'     => 'idTutor',
        ];
        if (!isset($allow[$tabla]) || $allow[$tabla] !== $idCol) return;
        if (!self::passwordNeedsRehash($hashActual)) return;

        $nuevo = self::hashPassword($password);
        $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET `password` = ? WHERE `$idCol` = ?");
        mysqli_stmt_bind_param($stmt, "si", $nuevo, $idVal);
        @mysqli_stmt_execute($stmt);
    }

    /**
     * Marca el instante de cambio de contraseña (invalida otras sesiones).
     * Tolerante a que la columna pwd_changed_at no exista todavía (deploy seguro).
     */
    public static function touchPasswordChanged($con, string $tabla, string $whereCol, $whereVal): void {
        $allowTablas = ['directores', 'profesores', 'estudiantes', 'tutores'];
        $allowCols = ['idDirector', 'idProfesor', 'idEstudiante', 'idTutor',
                      'emailDirector', 'emailProfesor', 'emailEstudiante', 'emailTutor'];
        if (!in_array($tabla, $allowTablas, true) || !in_array($whereCol, $allowCols, true)) return;
        $type = (strncmp($whereCol, 'id', 2) === 0) ? 'i' : 's';
        try {
            $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET `pwd_changed_at` = NOW() WHERE `$whereCol` = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, $type, $whereVal);
                mysqli_stmt_execute($stmt);
            }
        } catch (\Throwable $e) { /* columna ausente: ignorar */ }
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    public static function passwordNeedsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public static function escapeHtml($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

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

    /** Valida teléfono español: 9 dígitos comenzando por 6, 7, 8 o 9. */
    public static function validatePhone($phone) {
        $phone = str_replace([' ', '-', '.'], '', $phone);
        return preg_match('/^[6789][0-9]{8}$/', $phone) === 1;
    }

}

Security::initSession();
?>

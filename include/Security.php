<?php
class Security {

    // ══════════════════════════════════════════════════════════════════════
    // SESIÓN
    // ══════════════════════════════════════════════════════════════════════

    const CSRF_TOKEN_LENGTH    = 32;
    const CSRF_VALIDITY_SECONDS = 3600; // 1 hora
    const RATE_LIMIT_ATTEMPTS  = 5;
    const RATE_LIMIT_WINDOW    = 300;   // 5 minutos

    public static function initSession() {
        // Enviar cabeceras de seguridad globales
        if (!headers_sent()) {
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: strict-origin-when-cross-origin');
            // No añadimos un CSP estricto aquí para evitar dañar estilos/scripts en línea existentes
        }

        if (session_status() === PHP_SESSION_NONE) {
            // El flag Secure solo se activa sobre HTTPS para no romper el entorno de desarrollo local.
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

            @ini_set('session.use_strict_mode', '1'); // rechaza SIDs externos (previene session fixation)
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

    // Defensa de sesión para usuarios autenticados:
    // fingerprint de User-Agent (mitiga secuestro) + caducidad por inactividad.
    // No aplica a visitantes anónimos para no romper el token CSRF del login.
    public static function enforceSessionSecurity() {
        $authKeys = ['idAdmin', 'idProfesor', 'idEstudiante', 'idTutor', 'idSecretaria'];
        $isAuth = false;
        foreach ($authKeys as $k) {
            if (!empty($_SESSION[$k])) { $isAuth = true; break; }
        }
        if (!$isAuth) return;

        // Fingerprint estable (User-Agent). No usamos IP para no expulsar a usuarios móviles.
        $fp = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
        if (!isset($_SESSION['_fp'])) {
            // First request, store fingerprint
            $_SESSION['_fp'] = $fp;
            $_SESSION['_fp_mismatch_time'] = 0;
        } elseif (!hash_equals($_SESSION['_fp'], $fp)) {
            // Allow a grace period of 2 minutes for fingerprint mismatches (e.g., after browser updates)
            $now = time();
            $lastMismatch = $_SESSION['_fp_mismatch_time'] ?? 0;
            if ($lastMismatch === 0) {
                // Record first mismatch timestamp
                $_SESSION['_fp_mismatch_time'] = $now;
            } elseif ($now - $lastMismatch > 120) {
                // If mismatch persists beyond grace period, destroy session
                self::destroySession();
                return;
            }
            // Otherwise, keep session alive and will re‑check on next request
        } else {
            // Fingerprint matches, clear any previous mismatch record
            $_SESSION['_fp_mismatch_time'] = 0;
        }

        // Invalidación tras cambio de contraseña (revalida como máximo cada 60 s).
        // Si pwd_changed_at en BD es posterior al de esta sesión → sesión obsoleta.
        $roleMap = [
            'idAdmin'      => ['directores',  'idDirector'],
            'idProfesor'   => ['profesores',  'idProfesor'],
            'idEstudiante' => ['estudiantes', 'idEstudiante'],
            'idTutor'      => ['tutores',     'idTutor'],
            'idSecretaria' => ['secretarias', 'idSecretaria'],
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

        // Idle timeout configurable vía SESSION_TIMEOUT en .env.
        $timeout = 14400;
        if (class_exists('Config')) {
            $cfg = Config::getInstance();
            $timeout = max(300, (int)$cfg->getInteger('SESSION_TIMEOUT', 14400));
        }
        $now = time();
        if (isset($_SESSION['_last_activity']) && ($now - $_SESSION['_last_activity']) > $timeout) {
            self::destroySession();
            return;
        }
        $_SESSION['_last_activity'] = $now;

        // Cambio de contraseña obligatorio en el primer acceso.
        // Solo redirigimos navegaciones a /vistas/ (los controladores los bloquean sus propios Guards).
        // Se excluye la propia página de cambio para no crear bucles.
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
            if (PHP_VERSION_ID >= 70300) {
                setcookie(session_name(), '', [
                    'expires'  => time() - 42000,
                    'path'     => $p['path']   ?? '/',
                    'domain'   => $p['domain'] ?? '',
                    'secure'   => !empty($p['secure']),
                    'httponly' => true,
                    'samesite' => $p['samesite'] ?? 'Lax',
                ]);
            } else {
                setcookie(session_name(), '', time() - 42000,
                    ($p['path'] ?? '/') . '; SameSite=Lax', $p['domain'] ?? '', !empty($p['secure']), true);
            }
        }
        @session_destroy();
    }

    // Solo invocar en login/logout: la regeneración periódica destruye el token CSRF
    // en hosting compartido (LiteSpeed/cPanel) incluso con el flag keep-old-data activo.
    public static function regenerateSession() {
        $saved = $_SESSION;
        session_regenerate_id(true);
        $_SESSION = $saved;
    }

    // ══════════════════════════════════════════════════════════════════════
    // CSRF
    // ══════════════════════════════════════════════════════════════════════

    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token']      = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token = null, $rotate = true) {
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
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }

        if ($rotate) {
            // Rotate: invalidate used token so each form submission gets a fresh one
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        }
        return true;
    }

    // ══════════════════════════════════════════════════════════════════════
    // RATE LIMITING DE LOGIN
    // ══════════════════════════════════════════════════════════════════════

    // Rate limiting por sesión (por email). Complementa el límite de IP de validacion.php.
    public static function checkRateLimit($email, $maxAttempts = self::RATE_LIMIT_ATTEMPTS) {
        $attemptKey = 'rate_limit_' . md5($email);

        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = [
                'attempts'      => 0,
                'first_attempt' => time(),
                'blocked_until' => null,
            ];
        }

        $attempt = &$_SESSION[$attemptKey];
        $now = time();

        if ($attempt['blocked_until'] !== null && $now < $attempt['blocked_until']) {
            $remainingTime = $attempt['blocked_until'] - $now;
            return [
                'allowed'        => false,
                'message'        => "Se ha superado el límite de intentos permitidos. Por favor, inténtelo de nuevo en $remainingTime segundos.",
                'remaining_time' => $remainingTime,
            ];
        }

        if ($now - $attempt['first_attempt'] > self::RATE_LIMIT_WINDOW) {
            $attempt['attempts']      = 0;
            $attempt['first_attempt'] = $now;
            $attempt['blocked_until'] = null;
        }

        return [
            'allowed'      => true,
            'attempts'     => $attempt['attempts'],
            'max_attempts' => $maxAttempts,
        ];
    }

    public static function recordFailedLogin($email) {
        $attemptKey = 'rate_limit_' . md5($email);

        if (!isset($_SESSION[$attemptKey])) {
            $_SESSION[$attemptKey] = [
                'attempts'      => 0,
                'first_attempt' => time(),
                'blocked_until' => null,
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

    // ══════════════════════════════════════════════════════════════════════
    // CONTRASEÑAS
    // ══════════════════════════════════════════════════════════════════════

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

    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Rehashea (a coste 12) la contraseña tras un login correcto si el hash almacenado usa un coste/algoritmo más débil.
    // La tabla/columna se validan contra una lista blanca → seguro frente a inyección de nombre.
    public static function rehashOnLogin($con, string $tabla, string $idCol, $idVal, string $password, string $hashActual): void {
        $allow = [
            'directores'  => 'idDirector',
            'profesores'  => 'idProfesor',
            'estudiantes' => 'idEstudiante',
            'tutores'     => 'idTutor',
            'secretarias' => 'idSecretaria',
        ];
        if (!isset($allow[$tabla]) || $allow[$tabla] !== $idCol) return;
        if (!self::passwordNeedsRehash($hashActual)) return;

        $nuevo = self::hashPassword($password);
        $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET `password` = ? WHERE `$idCol` = ?");
        mysqli_stmt_bind_param($stmt, "si", $nuevo, $idVal);
        @mysqli_stmt_execute($stmt);
    }

    // Marca el instante de cambio de contraseña para invalidar otras sesiones activas.
    // Tolerante a que la columna pwd_changed_at no exista todavía (deploy seguro).
    public static function touchPasswordChanged($con, string $tabla, string $whereCol, $whereVal): void {
        $allowTablas = ['directores', 'profesores', 'estudiantes', 'tutores', 'secretarias'];
        $allowCols = ['idDirector', 'idProfesor', 'idEstudiante', 'idTutor', 'idSecretaria',
                      'emailDirector', 'emailProfesor', 'emailEstudiante', 'emailTutor', 'emailSecretaria'];
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

    // ══════════════════════════════════════════════════════════════════════
    // VALIDACIONES
    // ══════════════════════════════════════════════════════════════════════

    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return trim(strip_tags($input));
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Mínimo 8 caracteres, una mayúscula, una minúscula y un número.
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

    public static function validateDNI($dni) {
        $dni = strtoupper(trim($dni));
        if (!preg_match('/^[XYZ0-9][0-9]{7}[A-Z]$/', $dni)) {
            return false;
        }
        $nieMap = ['X' => '0', 'Y' => '1', 'Z' => '2'];
        $firstChar = substr($dni, 0, 1);
        if (isset($nieMap[$firstChar])) {
            $calcStr = $nieMap[$firstChar] . substr($dni, 1, 7);
        } else {
            $calcStr = substr($dni, 0, 8);
        }
        $validLetters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $dniNumber    = (int)$calcStr;
        $letter       = substr($dni, -1);
        return $letter === $validLetters[$dniNumber % 23];
    }

    // Valida teléfono español: 9 dígitos comenzando por 6, 7, 8 o 9.
    public static function validatePhone($phone) {
        $phone = str_replace([' ', '-', '.'], '', $phone);
        return preg_match('/^[6789][0-9]{8}$/', $phone) === 1;
    }

    // ══════════════════════════════════════════════════════════════════════
    // UTILIDADES
    // ══════════════════════════════════════════════════════════════════════

    // Returns the real client IP, honouring Cloudflare's CF-Connecting-IP header only
    // when REMOTE_ADDR is a known Cloudflare egress IP (prevents header spoofing).
    public static function clientIp(): string {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && self::isCloudflareIp($remote)) {
            return substr($_SERVER['HTTP_CF_CONNECTING_IP'], 0, 45);
        }
        return substr($remote, 0, 45);
    }

    private static function isCloudflareIp(string $ip): bool {
        static $cfRanges = [
            '173.245.48.0/20','103.21.244.0/22','103.22.200.0/22','103.31.4.0/22',
            '141.101.64.0/18','108.162.192.0/18','190.93.240.0/20','188.114.96.0/20',
            '197.234.240.0/22','198.41.128.0/17','162.158.0.0/15','104.16.0.0/13',
            '104.24.0.0/14','172.64.0.0/13','131.0.72.0/22',
        ];
        $ipLong = ip2long($ip);
        if ($ipLong === false) return false;
        foreach ($cfRanges as $range) {
            [$net, $bits] = explode('/', $range);
            $mask = ~((1 << (32 - (int)$bits)) - 1);
            if ((ip2long($net) & $mask) === ($ipLong & $mask)) return true;
        }
        return false;
    }

    public static function getCountryFromIP($ip = null) {
        if ($ip === null) {
            $ip = self::clientIp();
        }

        // CF-Connecting-IP is already resolved above in clientIp(); trust CF-IPCountry
        // only when REMOTE_ADDR is a genuine Cloudflare IP (prevents header spoofing).
        $remote = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && self::isCloudflareIp($remote)) {
            return strtoupper($_SERVER['HTTP_CF_IPCOUNTRY']);
        }
        
        // Localhost bypass
        if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0) {
            return 'ES'; // Treat localhost as allowed (Spain) to prevent dev lockout
        }

        // 2. Fallback to free API (ip-api.com)
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 0.4]]);
            $json = @file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode", false, $ctx);
            if ($json) {
                $data = json_decode($json, true);
                if (!empty($data['countryCode'])) {
                    return strtoupper($data['countryCode']);
                }
            }
        } catch (\Throwable $th) {}

        // Fallback to 'ES' to prevent locking out the admin if the free API is rate-limiting the server's public IP
        return 'ES';
    }

    public static function escapeHtml($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(array $data): void {
        if (ob_get_level() > 0) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

Security::initSession();

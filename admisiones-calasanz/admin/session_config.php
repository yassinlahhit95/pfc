<?php
// Secure session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie expires when browser closes
        'path' => '/',
        'domain' => '', 
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true, // Prevents JavaScript access to the cookie
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Session timeout logic (30 minutes)
$timeout_duration = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    if (strpos($_SERVER['PHP_SELF'], 'admin/') !== false) {
        header("Location: login.php");
    } else {
        header("Location: admin/login.php");
    }
    exit;
}
$_SESSION['last_activity'] = time();
?>

<?php
declare(strict_types=1);

require_once __DIR__ . '/../include/Security.php';
Security::initSession();
require_once __DIR__ . '/../include/I18n.php';

if (isset($_POST['lang'])) {
    I18n::setLang((string)$_POST['lang']);
}

// Redirect back to the referrer or login page
$referrer = $_SERVER['HTTP_REFERER'] ?? '../vistas/login.php';
header("Location: " . $referrer);
exit;

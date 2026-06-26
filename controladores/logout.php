<?php
require_once __DIR__ . '/../include/Security.php';
Security::initSession();
session_unset();
session_destroy();
header("Location: ../index.php");
exit;

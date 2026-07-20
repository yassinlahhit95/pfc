<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');
$blogRolBase = 'admin';
require __DIR__ . '/../../comunes/blog/insertar_impl.php';

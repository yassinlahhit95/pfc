<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_landing');
$blogRolBase = 'admin';
require __DIR__ . '/../../comunes/blog/borrar_impl.php';

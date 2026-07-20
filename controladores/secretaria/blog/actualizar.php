<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');
$blogRolBase = 'secretaria';
require __DIR__ . '/../../comunes/blog/actualizar_impl.php';

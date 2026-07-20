<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');
$rolBase = 'secretaria';
require __DIR__ . '/../../comunes/ofertaCiclos/actualizar_impl.php';

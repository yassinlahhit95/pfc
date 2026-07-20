<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');
$rolBase = 'admin';
require __DIR__ . '/../../comunes/ofertaCiclos/insertar_impl.php';

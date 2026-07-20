<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_landing');
$rolBase = 'admin';
require __DIR__ . '/../../comunes/ofertaCiclos/borrar_impl.php';

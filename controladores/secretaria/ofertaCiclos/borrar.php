<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_landing');
$rolBase = 'secretaria';
require __DIR__ . '/../../comunes/ofertaCiclos/borrar_impl.php';

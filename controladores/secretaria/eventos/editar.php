<?php
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_eventos');
$rolBase = 'secretaria';
require __DIR__ . '/../../comunes/eventos/editar_impl.php';

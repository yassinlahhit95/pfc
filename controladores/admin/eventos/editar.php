<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_eventos');
$rolBase = 'admin';
require __DIR__ . '/../../comunes/eventos/editar_impl.php';

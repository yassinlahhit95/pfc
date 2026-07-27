<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');
$rolBase = 'secretaria';
require __DIR__ . '/../../comunes/pagos/resolverComprobante_impl.php';

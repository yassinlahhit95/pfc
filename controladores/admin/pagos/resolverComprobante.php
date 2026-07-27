<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');
$rolBase = 'admin';
require __DIR__ . '/../../comunes/pagos/resolverComprobante_impl.php';

<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_landing');
require __DIR__ . '/../../comunes/ofertaCiclos/subir_imagen_impl.php';

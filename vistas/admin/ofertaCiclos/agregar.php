<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_ciclo'] ?? [];
unset($_SESSION['datos_ciclo']);

$rolBase = 'admin';

$titulo_pagina = "Nuevo Ciclo";
$seccion = 'ofertaCiclos';
include_once __DIR__ . "/../comunes/nav.php";
require __DIR__ . '/../../comunes/ofertaCiclos/_agregar.php';

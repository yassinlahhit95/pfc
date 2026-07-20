<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/landingCiclos.php";

$ciclos = listarTodosLosCiclosLanding();
$rolBase = 'admin';

$titulo_pagina = "AULAPRO | CATÁLOGO DE CICLOS";
$seccion = 'ofertaCiclos';
include_once __DIR__ . "/../comunes/nav.php";
require __DIR__ . '/../../comunes/ofertaCiclos/_gestion.php';

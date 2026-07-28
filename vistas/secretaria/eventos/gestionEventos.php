<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_eventos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/eventos.php";

$rolBase = 'secretaria';

$titulo_pagina = "AULAPRO | GESTIÓN DE EVENTOS";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";

require __DIR__ . '/../../comunes/eventos/_gestionEventos.php';

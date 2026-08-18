<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/landingCiclos.php";

$idLandingCiclo = (int)($_GET['idLandingCiclo'] ?? 0);
$ciclo = obtenerCicloLandingPorId($idLandingCiclo);

if (!$ciclo) {
    header("Location: gestion.php");
    exit;
}

$rolBase = 'admin';

$titulo_pagina = "Modificar Ciclo";
$seccion = 'ofertaCiclos';
include_once __DIR__ . "/../comunes/nav.php";
require __DIR__ . '/../../comunes/ofertaCiclos/_modificar.php';

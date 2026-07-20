<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/blog.php";

$datos = $_SESSION['datos_post'] ?? [];
unset($_SESSION['datos_post']);

$categorias = listarCategoriasBlog();
$blogRolBase = 'secretaria';

$titulo_pagina = "AULAPRO | NUEVA ENTRADA DEL BLOG";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
require __DIR__ . '/../../comunes/blog/_agregarPost.php';

<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/blog.php";

$posts = listarTodosLosPosts();
$blogRolBase = 'secretaria';

$titulo_pagina = "Blog del Centro";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
require __DIR__ . '/../../comunes/blog/_gestionBlog.php';

<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_landing');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/blog.php";

$idPost = (int)($_GET['idPost'] ?? 0);
$post = obtenerPostPorId($idPost);

if (!$post) {
    header("Location: gestionBlog.php");
    exit;
}

$categorias = listarCategoriasBlog();
$fechaValor = $post['fechaPublicacion'] ? date('Y-m-d\TH:i', strtotime($post['fechaPublicacion'])) : '';
$blogRolBase = 'admin';

$titulo_pagina = "Modificar Entrada del Blog";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
require __DIR__ . '/../../comunes/blog/_modificarPost.php';

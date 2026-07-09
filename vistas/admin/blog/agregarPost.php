<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
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

$titulo_pagina = "AULAPRO | NUEVA ENTRADA DEL BLOG";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVA ENTRADA</h1>
    <a href="gestionBlog.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/blog/insertar.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'titulo') ?>">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" maxlength="200" required
                       placeholder="Ej: Abierto el plazo de matrícula para el curso 2026/27"
                       value="<?= Security::escapeHtml($datos['titulo'] ?? '') ?>">
                <?= fieldError($errores, 'titulo') ?>
            </div>

            <div class="campo">
                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria" maxlength="80" list="categorias-blog"
                       placeholder="Ej: Admisiones, Eventos, FP Dual..."
                       value="<?= Security::escapeHtml($datos['categoria'] ?? '') ?>">
                <datalist id="categorias-blog">
                    <?php foreach ($categorias as $cat) { ?>
                    <option value="<?= Security::escapeHtml($cat['categoria']) ?>"></option>
                    <?php } ?>
                </datalist>
            </div>

            <div class="campo">
                <label for="autor">Autor (opcional)</label>
                <input type="text" id="autor" name="autor" maxlength="120"
                       placeholder="Ej: Secretaría del centro"
                       value="<?= Security::escapeHtml($datos['autor'] ?? '') ?>">
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaPublicacion') ?>">
                <label for="fechaPublicacion">Fecha de publicación</label>
                <input type="datetime-local" id="fechaPublicacion" name="fechaPublicacion"
                       value="<?= Security::escapeHtml($datos['fechaPublicacion'] ?? date('Y-m-d\TH:i')) ?>">
                <?= fieldError($errores, 'fechaPublicacion') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'imagen') ?>">
                <label for="imagen">Imagen de portada (JPG, PNG o WebP)</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
                <?= fieldError($errores, 'imagen') ?>
            </div>

            <div class="campo ancho-total">
                <label for="resumen">Resumen (aparece en las tarjetas del blog y de la landing)</label>
                <textarea id="resumen" name="resumen" rows="2" maxlength="500"
                          placeholder="Un par de frases que resuman la noticia..."><?= Security::escapeHtml($datos['resumen'] ?? '') ?></textarea>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'contenido') ?>">
                <label for="contenido">Contenido</label>
                <textarea id="contenido" name="contenido" rows="14" required
                          placeholder="Escribe aquí el contenido completo. Separa los párrafos con una línea en blanco."><?= Security::escapeHtml($datos['contenido'] ?? '') ?></textarea>
                <?= fieldError($errores, 'contenido') ?>
            </div>

            <div class="campo">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="publicado" value="1" <?= !isset($datos['titulo']) || !empty($datos['publicado']) ? 'checked' : '' ?> style="width:auto;">
                    Publicar (visible en el blog público)
                </label>
            </div>

            <div class="campo">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="destacado" value="1" <?= !empty($datos['destacado']) ? 'checked' : '' ?> style="width:auto;">
                    Destacar (se muestra primero)
                </label>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarPost" class="boton-primario" value="GUARDAR ENTRADA">
                <a href="gestionBlog.php" class="boton-secundario">CANCELAR</a>
            </div>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

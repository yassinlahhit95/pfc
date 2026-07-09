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

$titulo_pagina = "AULAPRO | MODIFICAR ENTRADA DEL BLOG";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR ENTRADA</h1>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php if ((int)$post['publicado'] === 1) { ?>
        <a href="/vistas/blog.php?post=<?= Security::escapeHtml($post['slug']) ?>" target="_blank" rel="noopener" class="boton-secundario">
            <i class="fas fa-arrow-up-right-from-square"></i> VER EN EL BLOG
        </a>
        <?php } ?>
        <a href="gestionBlog.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </div>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/blog/actualizar.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idPost" value="<?= (int)$post['idPost'] ?>">

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'titulo') ?>">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" maxlength="200" required
                       value="<?= Security::escapeHtml($post['titulo']) ?>">
                <?= fieldError($errores, 'titulo') ?>
            </div>

            <div class="campo">
                <label for="categoria">Categoría</label>
                <input type="text" id="categoria" name="categoria" maxlength="80" list="categorias-blog"
                       value="<?= Security::escapeHtml($post['categoria']) ?>">
                <datalist id="categorias-blog">
                    <?php foreach ($categorias as $cat) { ?>
                    <option value="<?= Security::escapeHtml($cat['categoria']) ?>"></option>
                    <?php } ?>
                </datalist>
            </div>

            <div class="campo">
                <label for="autor">Autor (opcional)</label>
                <input type="text" id="autor" name="autor" maxlength="120"
                       value="<?= Security::escapeHtml($post['autor']) ?>">
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaPublicacion') ?>">
                <label for="fechaPublicacion">Fecha de publicación</label>
                <input type="datetime-local" id="fechaPublicacion" name="fechaPublicacion"
                       value="<?= Security::escapeHtml($fechaValor) ?>">
                <?= fieldError($errores, 'fechaPublicacion') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'imagen') ?>">
                <label for="imagen">Imagen de portada (JPG, PNG o WebP)</label>
                <?php if (!empty($post['imagen'])) { ?>
                <img src="/public/uploads/blog/<?= Security::escapeHtml(basename($post['imagen'])) ?>" alt=""
                     style="max-height:90px;border-radius:8px;margin-bottom:8px;border:1px solid var(--border);">
                <?php } ?>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
                <?= fieldError($errores, 'imagen') ?>
                <?php if (!empty($post['imagen'])) { ?>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-top:6px;font-weight:normal;">
                    <input type="checkbox" name="quitarImagen" value="1" style="width:auto;"> Quitar la imagen actual
                </label>
                <?php } ?>
            </div>

            <div class="campo ancho-total">
                <label for="resumen">Resumen (aparece en las tarjetas del blog y de la landing)</label>
                <textarea id="resumen" name="resumen" rows="2" maxlength="500"><?= Security::escapeHtml($post['resumen']) ?></textarea>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'contenido') ?>">
                <label for="contenido">Contenido</label>
                <textarea id="contenido" name="contenido" rows="14" required><?= Security::escapeHtml($post['contenido']) ?></textarea>
                <?= fieldError($errores, 'contenido') ?>
            </div>

            <div class="campo">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="publicado" value="1" <?= (int)$post['publicado'] === 1 ? 'checked' : '' ?> style="width:auto;">
                    Publicar (visible en el blog público)
                </label>
            </div>

            <div class="campo">
                <label style="display:flex;align-items:center;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="destacado" value="1" <?= (int)$post['destacado'] === 1 ? 'checked' : '' ?> style="width:auto;">
                    Destacar (se muestra primero)
                </label>
            </div>

            <div class="acciones">
                <input type="submit" name="actualizarPost" class="boton-primario" value="GUARDAR CAMBIOS">
                <a href="gestionBlog.php" class="boton-secundario">CANCELAR</a>
            </div>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

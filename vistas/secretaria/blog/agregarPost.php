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

$titulo_pagina = "AULAPRO | NUEVA ENTRADA DEL BLOG";
$seccion = 'blog';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVA ENTRADA</h1>
    <a href="gestionBlog.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/blog/insertar.php" enctype="multipart/form-data">
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
                    <?php foreach ($categorias as $categoria) { ?>
                    <option value="<?= Security::escapeHtml($categoria['categoria']) ?>"></option>
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
                <label>Imagen de portada</label>
                <label class="zona-subida" for="imagen">
                    <i class="fas fa-image"></i>
                    <span>Elige una imagen de portada</span>
                    <small>JPG, PNG o WebP</small>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" style="display:none">
                </label>
                <?= fieldError($errores, 'imagen') ?>
            </div>

            <div class="campo ancho-total">
                <label for="resumen">Resumen (aparece en las tarjetas del blog y de la landing)</label>
                <textarea id="resumen" name="resumen" rows="2" maxlength="500"
                          placeholder="Un par de frases que resuman la noticia..."><?= Security::escapeHtml($datos['resumen'] ?? '') ?></textarea>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'contenido') ?>">
                <label for="contenido">Contenido</label>
                <div class="editor-toolbar" data-editor-toolbar="contenido">
                    <select class="editor-toolbar-select" data-cmd-select="formatBlock" title="Estilo de título">
                        <option value="">Título…</option>
                        <option value="P">Párrafo normal</option>
                        <option value="H1">Título 1</option>
                        <option value="H2">Título 2</option>
                        <option value="H3">Título 3</option>
                        <option value="H4">Título 4</option>
                        <option value="H5">Título 5</option>
                        <option value="H6">Título 6</option>
                    </select>
                    <span class="editor-toolbar-sep"></span>
                    <button type="button" data-cmd="bold" title="Negrita"><i class="fas fa-bold"></i></button>
                    <button type="button" data-cmd="italic" title="Cursiva"><i class="fas fa-italic"></i></button>
                    <button type="button" data-cmd="underline" title="Subrayado"><i class="fas fa-underline"></i></button>
                    <span class="editor-toolbar-sep"></span>
                    <label class="editor-toolbar-color" title="Color de texto">
                        <i class="fas fa-font"></i>
                        <input type="color" data-cmd-color="foreColor" value="#1d4ed8">
                    </label>
                    <label class="editor-toolbar-color" title="Color de resaltado">
                        <i class="fas fa-highlighter"></i>
                        <input type="color" data-cmd-color="hiliteColor" value="#fef08a">
                    </label>
                    <span class="editor-toolbar-sep"></span>
                    <button type="button" data-cmd="insertUnorderedList" title="Lista"><i class="fas fa-list-ul"></i></button>
                    <button type="button" data-cmd="insertOrderedList" title="Lista numerada"><i class="fas fa-list-ol"></i></button>
                    <button type="button" data-cmd="createLink" title="Insertar enlace"><i class="fas fa-link"></i></button>
                    <span class="editor-toolbar-sep"></span>
                    <button type="button" data-accion="imagen" title="Insertar imagen"><i class="fas fa-image"></i></button>
                    <button type="button" data-accion="video" title="Insertar vídeo"><i class="fas fa-video"></i></button>
                    <button type="button" data-cmd="removeFormat" title="Quitar formato"><i class="fas fa-eraser"></i></button>
                </div>
                <div class="editor-contenido" id="editor-contenido" contenteditable="true" data-placeholder="Escribe aquí el contenido completo..."></div>
                <input type="file" id="editor-imagen-input" accept="image/jpeg,image/png,image/webp" style="display:none;">
                <textarea id="contenido" name="contenido" style="display:none;"></textarea>
                <?= fieldError($errores, 'contenido') ?>
            </div>

            <div class="campo-checkbox-grupo campo-ancho-total">
                <label class="campo-checkbox">
                    <input type="checkbox" name="publicado" value="1" <?= !isset($datos['titulo']) || !empty($datos['publicado']) ? 'checked' : '' ?>>
                    Publicar (visible en el blog público)
                </label>
                <label class="campo-checkbox">
                    <input type="checkbox" name="destacado" value="1" <?= !empty($datos['destacado']) ? 'checked' : '' ?>>
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
<script src="../../../public/js/features/blog-editor.js?v=<?= @filemtime(__DIR__ . '/../../../public/js/features/blog-editor.js') ?>"></script>
<script>
iniciarEditorBlog({
    editorId: 'editor-contenido',
    textareaId: 'contenido',
    fileInputId: 'editor-imagen-input',
    uploadUrl: '../../../controladores/secretaria/blog/subir_imagen_contenido.php',
    csrfToken: document.querySelector('[name=csrf_token]').value,
    initialContent: <?= json_encode($datos['contenido'] ?? '') ?>
});
</script>

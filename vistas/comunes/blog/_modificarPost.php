<?php
// ══════════════════════════════════════════════════════════════════════
// Cuerpo compartido de vistas/{admin,secretaria}/blog/modificarPost.php
// El wrapper de cada rol ya resolvió el Guard, el nav, y debe definir
// $post, $categorias, $errores y $blogRolBase ('admin' | 'secretaria')
// antes de incluir este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/R2Client.php';
$imagenPortadaUrl = !empty($post['imagen']) ? R2Client::imagenUrl(
    __DIR__ . '/../../../public/uploads/blog/' . basename($post['imagen']),
    '/public/uploads/blog/' . basename($post['imagen']),
    'blog/' . basename($post['imagen'])
) : '';
?>

<div class="cabecera">
    <h1>Modificar Entrada</h1>
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
    <form method="POST" action="../../../controladores/<?= $blogRolBase ?>/blog/actualizar.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idPost" value="<?= (int)$post['idPost'] ?>">

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'titulo') ?>">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" maxlength="200" required
                       value="<?= Security::escapeHtml($post['titulo']) ?>">
                <?= fieldError($errores, 'titulo') ?>
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="categoria">Categoría</label>
                    <input type="text" id="categoria" name="categoria" maxlength="80" list="categorias-blog"
                           value="<?= Security::escapeHtml($post['categoria']) ?>">
                    <datalist id="categorias-blog">
                        <?php foreach ($categorias as $categoria) { ?>
                        <option value="<?= Security::escapeHtml($categoria['categoria']) ?>"></option>
                        <?php } ?>
                    </datalist>
                </div>

                <div class="campo">
                    <label for="autor">Autor (opcional)</label>
                    <input type="text" id="autor" name="autor" maxlength="120"
                           value="<?= Security::escapeHtml($post['autor']) ?>">
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'fechaPublicacion') ?>">
                    <label for="fechaPublicacion">Fecha de publicación</label>
                    <input type="datetime-local" id="fechaPublicacion" name="fechaPublicacion"
                           value="<?= Security::escapeHtml($fechaValor) ?>">
                    <?= fieldError($errores, 'fechaPublicacion') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'imagen') ?>">
                    <label for="imagen">Imagen de portada</label>
                    <?php if ($imagenPortadaUrl) { ?>
                    <img src="<?= Security::escapeHtml($imagenPortadaUrl) ?>" alt=""
                         style="max-height:90px;border-radius:8px;margin-bottom:8px;border:1px solid var(--border);">
                    <?php } ?>
                    <label class="zona-subida" for="imagen">
                        <i class="fas fa-image"></i>
                        <span><?= !empty($post['imagen']) ? 'Cambiar imagen de portada' : 'Elige una imagen de portada' ?></span>
                        <small>JPG, PNG o WebP</small>
                        <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" style="display:none">
                    </label>
                    <?= fieldError($errores, 'imagen') ?>
                    <?php if (!empty($post['imagen'])) { ?>
                    <label class="campo-checkbox" style="margin-top:6px;">
                        <input type="checkbox" name="quitarImagen" value="1"> Quitar la imagen actual
                    </label>
                    <?php } ?>
                </div>
            </div>

            <div class="campo ancho-total">
                <label for="resumen">Resumen (aparece en las tarjetas del blog y de la landing)</label>
                <textarea id="resumen" name="resumen" rows="2" maxlength="500"><?= Security::escapeHtml($post['resumen']) ?></textarea>
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
                    <input type="checkbox" name="publicado" value="1" <?= (int)$post['publicado'] === 1 ? 'checked' : '' ?>>
                    Publicar (visible en el blog público)
                </label>
                <label class="campo-checkbox">
                    <input type="checkbox" name="destacado" value="1" <?= (int)$post['destacado'] === 1 ? 'checked' : '' ?>>
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

<?php require __DIR__ . '/../footer.php'; ?>
<script src="../../../public/js/features/blog-editor.js?v=<?= @filemtime(__DIR__ . '/../../../public/js/features/blog-editor.js') ?>"></script>
<script>
iniciarEditorBlog({
    editorId: 'editor-contenido',
    textareaId: 'contenido',
    fileInputId: 'editor-imagen-input',
    uploadUrl: '../../../controladores/<?= $blogRolBase ?>/blog/subir_imagen_contenido.php',
    csrfToken: document.querySelector('[name=csrf_token]').value,
    initialContent: <?= Security::jsonEncodeSafe($post['contenido']) ?>
});
</script>

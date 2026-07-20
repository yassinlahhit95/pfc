<?php
// ══════════════════════════════════════════════════════════════════════
// Cuerpo compartido de vistas/{admin,secretaria}/ofertaCiclos/modificar.php
// El wrapper de cada rol ya resolvió el Guard, el nav, y debe definir
// $ciclo, $errores y $rolBase ('admin' | 'secretaria') antes de incluir
// este archivo.
// ══════════════════════════════════════════════════════════════════════
?>

<div class="cabecera">
    <h1>MODIFICAR CICLO</h1>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php if ((int)$ciclo['publicado'] === 1) { ?>
        <a href="/vistas/ciclos.php?ciclo=<?= Security::escapeHtml($ciclo['slug']) ?>" target="_blank" rel="noopener" class="boton-secundario">
            <i class="fas fa-arrow-up-right-from-square"></i> VER FICHA
        </a>
        <?php } ?>
        <a href="gestion.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </div>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/<?= $rolBase ?>/ofertaCiclos/actualizar.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idLandingCiclo" value="<?= (int)$ciclo['idLandingCiclo'] ?>">

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'titulo') ?>">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" maxlength="150" required
                       value="<?= Security::escapeHtml($ciclo['titulo']) ?>">
                <?= fieldError($errores, 'titulo') ?>
            </div>

            <div class="campo">
                <label for="etiqueta">Etiqueta</label>
                <input type="text" id="etiqueta" name="etiqueta" maxlength="60"
                       value="<?= Security::escapeHtml($ciclo['etiqueta']) ?>">
            </div>

            <div class="campo">
                <label for="precio">Precio</label>
                <input type="text" id="precio" name="precio" maxlength="60"
                       value="<?= Security::escapeHtml($ciclo['precio']) ?>">
            </div>

            <div class="campo">
                <label for="duracion">Duración</label>
                <input type="text" id="duracion" name="duracion" maxlength="60"
                       value="<?= Security::escapeHtml($ciclo['duracion']) ?>">
            </div>

            <div class="campo">
                <label for="modalidad">Modalidad</label>
                <input type="text" id="modalidad" name="modalidad" maxlength="60"
                       value="<?= Security::escapeHtml($ciclo['modalidad']) ?>">
            </div>

            <div class="campo">
                <label for="orden">Orden en el catálogo</label>
                <input type="number" id="orden" name="orden" min="0" step="1"
                       value="<?= (int)$ciclo['orden'] ?>">
            </div>

            <div class="campo<?= fieldClass($errores, 'imagen') ?>">
                <label>Imagen de portada</label>
                <?php if (!empty($ciclo['imagen'])) { ?>
                <img src="/public/uploads/ofertaCiclos/<?= Security::escapeHtml(basename($ciclo['imagen'])) ?>" alt=""
                     style="max-height:90px;border-radius:8px;margin-bottom:8px;border:1px solid var(--border);">
                <?php } ?>
                <label class="zona-subida" for="imagen">
                    <i class="fas fa-image"></i>
                    <span><?= !empty($ciclo['imagen']) ? 'Cambiar imagen de portada' : 'Elige una imagen de portada' ?></span>
                    <small>JPG, PNG o WebP</small>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" style="display:none">
                </label>
                <?= fieldError($errores, 'imagen') ?>
                <?php if (!empty($ciclo['imagen'])) { ?>
                <label class="campo-checkbox" style="margin-top:6px;">
                    <input type="checkbox" name="quitarImagen" value="1"> Quitar la imagen actual
                </label>
                <?php } ?>
            </div>

            <div class="campo ancho-total">
                <label for="resumen">Resumen (aparece en las tarjetas del catálogo)</label>
                <textarea id="resumen" name="resumen" rows="2" maxlength="300"><?= Security::escapeHtml($ciclo['resumen']) ?></textarea>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'descripcion') ?>">
                <label for="descripcion">Descripción completa (temario, requisitos, salidas profesionales...)</label>
                <div class="editor-toolbar" data-editor-toolbar="descripcion">
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
                <div class="editor-contenido" id="editor-descripcion" contenteditable="true" data-placeholder="Escribe aquí el temario, requisitos de acceso, salidas profesionales..."></div>
                <input type="file" id="editor-imagen-input" accept="image/jpeg,image/png,image/webp" style="display:none;">
                <textarea id="descripcion" name="descripcion" style="display:none;"></textarea>
                <?= fieldError($errores, 'descripcion') ?>
            </div>

            <div class="campo-checkbox-grupo campo-ancho-total">
                <label class="campo-checkbox">
                    <input type="checkbox" name="publicado" value="1" <?= (int)$ciclo['publicado'] === 1 ? 'checked' : '' ?>>
                    Publicar (visible en el catálogo público)
                </label>
                <label class="campo-checkbox">
                    <input type="checkbox" name="destacado" value="1" <?= (int)$ciclo['destacado'] === 1 ? 'checked' : '' ?>>
                    Destacar (aparece primero)
                </label>
            </div>

            <div class="acciones">
                <input type="submit" name="actualizarCiclo" class="boton-primario" value="GUARDAR CAMBIOS">
                <a href="gestion.php" class="boton-secundario">CANCELAR</a>
            </div>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../footer.php'; ?>
<script src="../../../public/js/features/blog-editor.js?v=<?= @filemtime(__DIR__ . '/../../../public/js/features/blog-editor.js') ?>"></script>
<script>
iniciarEditorBlog({
    editorId: 'editor-descripcion',
    textareaId: 'descripcion',
    fileInputId: 'editor-imagen-input',
    uploadUrl: '../../../controladores/<?= $rolBase ?>/ofertaCiclos/subir_imagen_contenido.php',
    csrfToken: document.querySelector('[name=csrf_token]').value,
    initialContent: <?= json_encode($ciclo['descripcion']) ?>
});
</script>

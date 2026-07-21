<?php
// ══════════════════════════════════════════════════════════════════════
// Cuerpo compartido de vistas/{admin,secretaria}/ofertaCiclos/agregar.php
// El wrapper de cada rol ya resolvió el Guard, el nav, y debe definir
// $datos, $errores y $rolBase ('admin' | 'secretaria') antes de incluir
// este archivo.
// ══════════════════════════════════════════════════════════════════════

// Ciclos académicos ya dados de alta (verCiclos.php) para poder autorrellenar
// el formulario del catálogo público a partir de uno existente. landing_ciclos
// sigue siendo una tabla independiente (contenido de marketing): esto no
// guarda ningún vínculo en BD, solo copia valores al elegir un ciclo.
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/niveles.php';
require_once __DIR__ . '/../../../modelos/academico_config.php';

$listaNivelesOferta = listarNiveles();

$cursosPorCiclo = [];
foreach (listarTodosLosCursosAcademicos() as $curso) {
    $cursosPorCiclo[$curso['idCiclo']] = ($cursosPorCiclo[$curso['idCiclo']] ?? 0) + 1;
}
$ciclosParaSelector = array_map(function ($ciclo) use ($cursosPorCiclo) {
    return [
        'idCiclo'       => (int)$ciclo['idCiclo'],
        'nombreCiclo'   => $ciclo['nombreCiclo'],
        'nombreNivel'   => $ciclo['nombreNivel'],
        'duracionAnios' => $cursosPorCiclo[$ciclo['idCiclo']] ?? 2,
        'precioTexto'   => $ciclo['precioCiclo'] > 0 ? number_format((float)$ciclo['precioCiclo'], 2, ',', '.') . ' € /curso' : '',
    ];
}, listarTodosLosCiclos());
?>

<div class="cabecera">
    <h1>NUEVO CICLO</h1>
    <a href="gestion.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel margen-abajo">
    <h3 style="margin-top:0;">Basar en un ciclo académico existente (opcional)</h3>
    <p class="texto-suave" style="margin-top:-8px;">Autorrellena título, etiqueta y precio a partir de un ciclo ya dado de alta en Académico. Podrás editar cualquier campo antes de guardar.</p>
    <div class="formulario">
        <div class="form-fila">
            <div class="campo">
                <label for="ocFiltroNivel">Tipo de grado</label>
                <select id="ocFiltroNivel" onchange="ocFiltrarCiclos()">
                    <option value="">-- Todos --</option>
                    <?php foreach ($listaNivelesOferta as $nivelOferta): ?>
                    <option value="<?= Security::escapeHtml($nivelOferta['nombreNivel']) ?>"><?= Security::escapeHtml($nivelOferta['nombreNivel']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="ocFiltroDuracion">Duración</label>
                <select id="ocFiltroDuracion" onchange="ocFiltrarCiclos()">
                    <option value="">-- Todas --</option>
                    <option value="1">1 año</option>
                    <option value="2">2 años</option>
                </select>
            </div>
        </div>
        <div class="form-fila">
            <div class="campo">
                <label for="ocCicloExistente">Ciclo académico</label>
                <select id="ocCicloExistente">
                    <option value="">-- Selecciona un ciclo --</option>
                </select>
            </div>
            <div class="campo" style="display:flex;align-items:flex-end;">
                <button type="button" class="boton-secundario" id="ocUsarCiclo"><i class="fas fa-arrow-down"></i> Usar estos datos</button>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/<?= $rolBase ?>/ofertaCiclos/insertar.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'titulo') ?>">
                <label for="titulo">Título</label>
                <input type="text" id="titulo" name="titulo" maxlength="150" required
                       placeholder="Ej: Desarrollo de Aplicaciones Multiplataforma"
                       value="<?= Security::escapeHtml($datos['titulo'] ?? '') ?>">
                <?= fieldError($errores, 'titulo') ?>
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="etiqueta">Etiqueta</label>
                    <input type="text" id="etiqueta" name="etiqueta" maxlength="60"
                           placeholder="Ej: Grado Superior"
                           value="<?= Security::escapeHtml($datos['etiqueta'] ?? '') ?>">
                </div>

                <div class="campo">
                    <label for="precio">Precio</label>
                    <input type="text" id="precio" name="precio" maxlength="60"
                           placeholder="Ej: 1.200 € /curso"
                           value="<?= Security::escapeHtml($datos['precio'] ?? '') ?>">
                </div>
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="duracion">Duración</label>
                    <input type="text" id="duracion" name="duracion" maxlength="60"
                           placeholder="Ej: 2.000 horas / 2 cursos"
                           value="<?= Security::escapeHtml($datos['duracion'] ?? '') ?>">
                </div>

                <div class="campo">
                    <label for="modalidad">Modalidad</label>
                    <input type="text" id="modalidad" name="modalidad" maxlength="60"
                           placeholder="Ej: Presencial"
                           value="<?= Security::escapeHtml($datos['modalidad'] ?? '') ?>">
                </div>
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="orden">Orden en el catálogo</label>
                    <input type="number" id="orden" name="orden" min="0" step="1"
                           value="<?= Security::escapeHtml($datos['orden'] ?? '0') ?>">
                </div>

                <div class="campo<?= fieldClass($errores, 'imagen') ?>">
                    <label for="imagen">Imagen de portada</label>
                    <label class="zona-subida" for="imagen">
                        <i class="fas fa-image"></i>
                        <span>Elige una imagen de portada</span>
                        <small>JPG, PNG o WebP</small>
                        <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" style="display:none">
                    </label>
                    <?= fieldError($errores, 'imagen') ?>
                </div>
            </div>

            <div class="campo ancho-total">
                <label for="resumen">Resumen (aparece en las tarjetas del catálogo)</label>
                <textarea id="resumen" name="resumen" rows="2" maxlength="300"
                          placeholder="Un par de frases que resuman el ciclo..."><?= Security::escapeHtml($datos['resumen'] ?? '') ?></textarea>
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
                    <input type="checkbox" name="publicado" value="1" <?= !isset($datos['titulo']) || !empty($datos['publicado']) ? 'checked' : '' ?>>
                    Publicar (visible en el catálogo público)
                </label>
                <label class="campo-checkbox">
                    <input type="checkbox" name="destacado" value="1" <?= !empty($datos['destacado']) ? 'checked' : '' ?>>
                    Destacar (aparece primero)
                </label>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarCiclo" class="boton-primario" value="GUARDAR CICLO">
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
    initialContent: <?= json_encode($datos['descripcion'] ?? '') ?>
});

var ocCiclos = <?= json_encode($ciclosParaSelector) ?>;

function ocFiltrarCiclos() {
    var nivel = $('#ocFiltroNivel').val();
    var duracion = $('#ocFiltroDuracion').val();
    var $sel = $('#ocCicloExistente').empty().append($('<option>').val('').text('-- Selecciona un ciclo --'));

    $.each(ocCiclos, function (i, ciclo) {
        var pasaNivel = !nivel || ciclo.nombreNivel === nivel;
        var pasaDuracion = !duracion || String(ciclo.duracionAnios) === duracion;
        if (pasaNivel && pasaDuracion) {
            $sel.append($('<option>').val(ciclo.idCiclo).text(ciclo.nombreCiclo));
        }
    });
}

$('#ocUsarCiclo').on('click', function () {
    var idCiclo = $('#ocCicloExistente').val();
    if (!idCiclo) {
        if (window.Toast) Toast.show('Selecciona primero un ciclo académico', 'info');
        return;
    }
    var ciclo = ocCiclos.filter(function (c) { return String(c.idCiclo) === String(idCiclo); })[0];
    if (!ciclo) return;

    $('#titulo').val(ciclo.nombreCiclo);
    $('#etiqueta').val(ciclo.nombreNivel);
    if (ciclo.precioTexto) $('#precio').val(ciclo.precioTexto);

    if (window.Toast) Toast.show('Datos autorrellenados. Revísalos antes de guardar.', 'success');
});

ocFiltrarCiclos();
</script>

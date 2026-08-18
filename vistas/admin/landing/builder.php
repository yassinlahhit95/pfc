<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/AssetMin.php";
FeatureGuard::requirePage('feature_landing');

require_once __DIR__ . "/../../../modelos/landing.php";
require_once __DIR__ . "/../../../include/landing/secciones.php";
require_once __DIR__ . "/../../../include/landing/plantillas.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$landingCfg = obtenerLandingConfig();
if (empty($landingCfg['plantilla'])) {
    header("Location: onboarding.php");
    exit;
}
$borrador   = listarSeccionesLanding('draft');
$publicadas = listarSeccionesLanding('live');
$tipos      = landing_tipos();
$ajustes    = json_decode($landingCfg['ajustes'] ?? '', true) ?: [];

// Estado del borrador respecto a lo publicado
$normalizar = function (array $filas): array {
    return array_map(fn($fila) => [$fila['tipo'], (int)$fila['orden'], (int)$fila['visible'], $fila['contenido']], $filas);
};
$hayCambios = $normalizar($borrador) != $normalizar($publicadas)
    || ($landingCfg['ajustes'] ?? '') != ($landingCfg['ajustes_pub'] ?? '')
    || $landingCfg['plantilla'] != ($landingCfg['plantilla_pub'] ?? '');

if (empty($landingCfg['publicadoEn'])) {
    $estadoClase = 'naranja';
    $estadoTexto = 'Sin publicar';
} elseif ($hayCambios) {
    $estadoClase = 'azul';
    $estadoTexto = 'Borrador con cambios';
} else {
    $estadoClase = 'verde';
    $estadoTexto = 'Publicado el ' . date('d/m/Y H:i', strtotime($landingCfg['publicadoEn']));
}

// Secciones del borrador con el contenido decodificado (para el editor JS)
$seccionesJs = array_map(fn($fila) => [
    'idSeccion' => (int)$fila['idSeccion'],
    'tipo'      => $fila['tipo'],
    'visible'   => (int)$fila['visible'],
    'contenido' => json_decode($fila['contenido'] ?? '{}', true) ?: [],
], $borrador);

$titulo_pagina = "Constructor de la Web";
$seccion       = 'landing';
include_once __DIR__ . '/../comunes/nav.php';
?>
<link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/landing-builder.css') ?>">

<div class="cabecera">
    <h1><i class="fas fa-globe"></i> Página web pública</h1>
    <div class="acciones-pagina">
        <span class="lb-inline-status" id="lb-inline-status"><i class="fas fa-check-circle"></i> <span class="lb-status-txt">Guardado</span></span>
        <span class="texto-estado <?= $estadoClase ?>" id="lb-estado"><?= Security::escapeHtml($estadoTexto) ?></span>
        <a href="plantillas.php" class="boton-secundario"><i class="fas fa-palette"></i> Plantillas</a>
        <a href="../../../" target="_blank" rel="noopener" class="boton-secundario"><i class="fas fa-arrow-up-right-from-square"></i> Ver web</a>
        <button type="button" class="boton-secundario" id="lb-descartar" <?= empty($landingCfg['publicadoEn']) ? 'disabled' : '' ?>>
            <i class="fas fa-rotate-left"></i> Descartar cambios
        </button>
        <button type="button" class="boton-primario" id="lb-publicar">
            <i class="fas fa-cloud-arrow-up"></i> Publicar
        </button>
    </div>
</div>

<input type="hidden" id="lb-csrf" value="<?= Security::generateCSRFToken() ?>">

<div class="lb-layout">

    <!-- ══════════ Columna izquierda: secciones + ajustes ══════════ -->
    <div class="lb-lateral">

        <div class="panel lb-panel-secciones">
            <div class="lb-panel-titulo">
                <h3><i class="fas fa-layer-group"></i> Secciones</h3>
                <button type="button" class="boton-primario boton-pequeno" id="lb-abrir-agregar">
                    <i class="fas fa-plus"></i> Añadir
                </button>
            </div>

            <?php if (!$borrador): ?>
            <div class="panel-vacio">
                <div class="panel-vacio-icono"><i class="fas fa-palette"></i></div>
                <div class="panel-vacio-titulo">El borrador está vacío</div>
                <div class="panel-vacio-desc">Empieza aplicando una plantilla o añade secciones una a una.</div>
                <a href="plantillas.php" class="boton-primario" style="margin-top:12px;">Elegir plantilla</a>
            </div>
            <?php endif; ?>

            <ul class="lb-lista" id="lb-lista">
                <?php foreach ($borrador as $fila):
                    $tipoInfo = $tipos[$fila['tipo']] ?? null;
                    if (!$tipoInfo) continue; ?>
                <li class="lb-item<?= (int)$fila['visible'] ? '' : ' lb-item-oculto' ?>" draggable="true" data-id="<?= (int)$fila['idSeccion'] ?>" data-tipo="<?= Security::escapeHtml($fila['tipo']) ?>">
                    <span class="lb-item-grip" title="Arrastra para reordenar"><i class="fas fa-grip-vertical"></i></span>
                    <span class="lb-item-icono"><i class="fas <?= Security::escapeHtml($tipoInfo['icono']) ?>"></i></span>
                    <span class="lb-item-nombre"><?= Security::escapeHtml($tipoInfo['nombre']) ?></span>
                    <span class="lb-item-acciones">
                        <button type="button" class="lb-item-btn lb-toggle-visible" title="<?= (int)$fila['visible'] ? 'Ocultar' : 'Mostrar' ?>">
                            <i class="fas <?= (int)$fila['visible'] ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                        </button>
                        <button type="button" class="lb-item-btn lb-editar" title="Editar contenido">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="lb-item-btn lb-item-btn-peligro lb-borrar-seccion" title="Eliminar" data-nombre="<?= Security::escapeHtml($tipoInfo['nombre']) ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="panel lb-panel-ajustes">
            <div class="lb-panel-titulo">
                <h3><i class="fas fa-sliders"></i> Ajustes globales</h3>
            </div>
            <form id="lb-form-ajustes" class="lb-ajustes">
                <label class="lb-campo">
                    <span>Color de acento</span>
                    <div class="lb-color-picker">
                        <div class="lb-color-wrap">
                            <input type="color" name="colorAcento" value="<?= Security::escapeHtml($ajustes['colorAcento'] ?? '#1d4ed8') ?>">
                            <code><?= Security::escapeHtml($ajustes['colorAcento'] ?? '#1d4ed8') ?></code>
                        </div>
                        <div class="lb-swatches">
                            <?php foreach (['#1d4ed8', '#4338ca', '#7c3aed', '#059669', '#0891b2', '#d97706', '#dc2626', '#334155'] as $sw): ?>
                            <button type="button" class="lb-swatch" style="background-color:<?= $sw ?>" data-color="<?= $sw ?>" title="<?= $sw ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </label>
                <label class="lb-campo">
                    <span>Título SEO (pestaña del navegador)</span>
                    <input type="text" name="tituloSeo" maxlength="150" value="<?= Security::escapeHtml($ajustes['tituloSeo'] ?? '') ?>" placeholder="Nombre del centro — FP oficial">
                </label>
                <label class="lb-campo">
                    <span>Descripción SEO</span>
                    <textarea name="descripcionSeo" rows="2" maxlength="300" placeholder="Descripción que aparece en Google"><?= Security::escapeHtml($ajustes['descripcionSeo'] ?? '') ?></textarea>
                </label>
                <label class="lb-campo lb-campo-check">
                    <input type="checkbox" name="mostrarTopbar" value="si" <?= ($ajustes['mostrarTopbar'] ?? 'si') === 'si' ? 'checked' : '' ?>>
                    <span>Mostrar barra superior de contacto (teléfono, email, redes)</span>
                </label>
                <details class="lb-redes">
                    <summary>Redes sociales</summary>
                    <?php foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X / Twitter', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $red => $nombreRed): ?>
                    <label class="lb-campo">
                        <span><?= $nombreRed ?></span>
                        <input type="url" name="red_<?= $red ?>" maxlength="255" placeholder="https://…"
                               value="<?= Security::escapeHtml($ajustes['redes'][$red] ?? '') ?>">
                    </label>
                    <?php endforeach; ?>
                </details>
                <button type="submit" class="boton-secundario"><i class="fas fa-floppy-disk"></i> Guardar ajustes</button>
            </form>
        </div>
    </div>

    <!-- ══════════ Columna derecha: previsualización ══════════ -->
    <div class="panel lb-preview-panel">
        <div class="lb-preview-barra">
            <span class="lb-preview-titulo"><i class="fas fa-eye"></i> Previsualización del borrador</span>
            <div class="lb-preview-dispositivos">
                <button type="button" class="lb-disp activo" data-ancho="100%" title="Escritorio"><i class="fas fa-desktop"></i></button>
                <button type="button" class="lb-disp" data-ancho="768px" title="Tableta"><i class="fas fa-tablet-screen-button"></i></button>
                <button type="button" class="lb-disp" data-ancho="390px" title="Móvil"><i class="fas fa-mobile-screen"></i></button>
            </div>
            <button type="button" class="lb-item-btn" id="lb-toggle-lateral" title="Ocultar barra lateral"><i class="fas fa-expand"></i></button>
            <button type="button" class="lb-item-btn" id="lb-recargar" title="Recargar previsualización"><i class="fas fa-rotate-right"></i></button>
        </div>
        <div class="lb-preview-marco">
            <iframe id="lb-iframe" title="Previsualización de la landing"></iframe>
        </div>
    </div>
</div>

<!-- ══════════ Panel deslizante de edición ══════════ -->
<div class="lb-editor-fondo" id="lb-editor-fondo"></div>
<aside class="lb-editor" id="lb-editor" aria-hidden="true">
    <div class="lb-editor-cabecera">
        <h3 id="lb-editor-titulo"><i class="fas fa-pen"></i> Editar sección</h3>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="lb-inline-status" id="lb-editor-status"><i class="fas fa-check-circle"></i> <span class="lb-status-txt">Guardado</span></span>
            <button type="button" class="lb-item-btn" id="lb-editor-cerrar" title="Cerrar"><i class="fas fa-xmark"></i></button>
        </div>
    </div>
    <div class="lb-editor-tabs" id="lb-editor-tabs">
        <button type="button" class="lb-tab-btn activo" data-tab="contenido"><i class="fas fa-pen-nib"></i> Contenido</button>
        <button type="button" class="lb-tab-btn" data-tab="estilo"><i class="fas fa-paintbrush"></i> Estilo</button>
        <button type="button" class="lb-tab-btn" data-tab="avanzado"><i class="fas fa-sliders"></i> Avanzado</button>
    </div>
    <form id="lb-editor-form" class="lb-editor-cuerpo"></form>
    <div class="lb-editor-pie">
        <button type="button" class="boton-secundario" id="lb-editor-cancelar">Cancelar</button>
        <button type="button" class="boton-primario" id="lb-editor-guardar"><i class="fas fa-floppy-disk"></i> Guardar sección</button>
    </div>
</aside>

<!-- ══════════ Modal añadir sección (con Previsualización Hover en Directo) ══════════ -->
<div class="lb-modal" id="lb-modal-agregar">
    <div class="lb-modal-caja lb-modal-catalogo-wrap panel">
        <div class="lb-panel-titulo">
            <h3><i class="fas fa-plus"></i> Catálogo de Secciones</h3>
            <button type="button" class="lb-item-btn" id="lb-agregar-cerrar" title="Cerrar"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="lb-catalogo-dos-col">
            <div class="lb-catalogo-lista">
                <?php foreach ($tipos as $tipoClave => $tipoInfo): ?>
                <button type="button" class="lb-catalogo-item" data-tipo="<?= Security::escapeHtml($tipoClave) ?>"
                        data-icono="fas <?= Security::escapeHtml($tipoInfo['icono']) ?>"
                        data-nombre="<?= Security::escapeHtml($tipoInfo['nombre']) ?>"
                        data-desc="<?= Security::escapeHtml($tipoInfo['descripcion'] ?? '') ?>">
                    <i class="fas <?= Security::escapeHtml($tipoInfo['icono']) ?>"></i>
                    <div class="lb-catalogo-texto">
                        <span class="lb-catalogo-nombre"><?= Security::escapeHtml($tipoInfo['nombre']) ?></span>
                        <?php if (!empty($tipoInfo['descripcion'])): ?>
                        <span class="lb-catalogo-desc"><?= Security::escapeHtml($tipoInfo['descripcion']) ?></span>
                        <?php endif; ?>
                    </div>
                </button>
                <?php endforeach; ?>
            </div>
            <div class="lb-catalogo-preview-pane" id="lb-catalogo-preview-pane">
                <div class="lb-preview-pane-header">
                    <span class="lb-preview-pane-tag"><i class="fas fa-eye"></i> Vista Previa</span>
                    <h4 id="lb-preview-pane-title">Pasa el ratón sobre una sección</h4>
                </div>
                <p class="lb-preview-pane-desc" id="lb-preview-pane-desc">Descubre el diseño y estructura visual de cada componente antes de insertarlo en la página web.</p>
                <div class="lb-preview-pane-card" id="lb-preview-pane-card">
                    <div class="lb-preview-mock-hero">
                        <div class="lb-mock-bar"></div>
                        <div class="lb-mock-title"></div>
                        <div class="lb-mock-sub"></div>
                        <div class="lb-mock-btn"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ Modal Selector de Iconos ══════════ -->
<div class="lb-modal" id="lb-modal-iconos">
    <div class="lb-modal-caja panel" style="width: 620px; max-width: 95vw;">
        <div class="lb-panel-titulo">
            <h3><i class="fas fa-icons"></i> Seleccionar Icono</h3>
            <button type="button" class="lb-item-btn" id="lb-iconos-cerrar" title="Cerrar"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="lb-iconos-busqueda-wrap">
            <input type="search" id="lb-iconos-busqueda" placeholder="Buscar icono (ej: graduación, maletín, tecnología...)" class="lb-input-busqueda">
        </div>
        <div class="lb-iconos-grid" id="lb-iconos-grid"></div>
    </div>
</div>

<!-- ══════════ Modal biblioteca de imágenes/vídeos ══════════ -->
<div class="lb-modal" id="lb-modal-biblioteca">
    <div class="lb-modal-caja panel">
        <div class="lb-panel-titulo">
            <h3><i class="fas fa-photo-film"></i> Biblioteca de archivos</h3>
            <button type="button" class="lb-item-btn" id="lb-biblioteca-cerrar" title="Cerrar"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="lb-biblioteca-grid" id="lb-biblioteca-grid">
            <p class="lb-biblioteca-cargando">Cargando…</p>
        </div>
    </div>
</div>

<script>
window.LANDING_TIPOS     = <?= Security::jsonEncodeSafe($tipos) ?>;
window.LANDING_SECCIONES = <?= Security::jsonEncodeSafe($seccionesJs) ?>;
</script>
<script src="<?= AssetMin::url(__DIR__, '../../../public/js/features/landing-builder.js') ?>"></script>

<?php include '../comunes/footer.php'; ?>

<?php
require_once __DIR__ . "/../../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_landing');

require_once __DIR__ . "/../../../../landing-system/engine/modelo.php";
require_once __DIR__ . "/../../../../landing-system/engine/plantillas.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$landingCfg = obtenerLandingConfig();
$plantillas = landing_plantillas();

$titulo_pagina = "AULAPRO | PLANTILLAS DE LA WEB";
$seccion       = 'landing';
include_once __DIR__ . '/../../../vistas/admin/comunes/nav.php';
?>

<div class="cabecera">
    <h1><i class="fas fa-palette"></i> Plantillas de la web pública</h1>
    <div class="acciones-pagina">
        <a href="builder.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver al constructor</a>
    </div>
</div>

<div class="panel margen-abajo">
    <p class="texto-suave" style="margin:0;">
        Elige una plantilla como punto de partida. Al aplicarla se <strong>reemplaza el borrador actual</strong>
        (la web publicada no cambia hasta que pulses «Publicar» en el constructor). Después podrás editar
        todos los textos e imágenes, y añadir, quitar o reordenar secciones.<br>
        <i class="fas fa-eye" style="color:var(--accent);margin-top:6px;display:inline-block;"></i>
        <span style="font-size:0.85rem;"> Pasa el cursor sobre una plantilla para ver una previsualización.</span>
    </p>
</div>

<div class="plantillas-grid">
    <?php foreach ($plantillas as $slug => $p):
        $esActual = ($landingCfg['plantilla'] === $slug); ?>
    <article class="panel plantilla-card<?= $esActual ? ' plantilla-actual' : '' ?>"
             data-slug="<?= Security::escapeHtml($slug) ?>">
        <div class="plantilla-thumb">
            <img src="/landing-system/assets/imagenes/<?= Security::escapeHtml($p['thumbnail']) ?>"
                 alt="<?= Security::escapeHtml($p['nombre']) ?>">
            <?php if ($esActual): ?>
            <span class="texto-estado verde plantilla-badge">Plantilla del borrador</span>
            <?php endif; ?>
            <!-- Botón de previsualización -->
            <button type="button" class="plantilla-preview-btn"
                    data-preview-url="/?preview-template=<?= urlencode($slug) ?>"
                    title="Previsualizar <?= Security::escapeHtml($p['nombre']) ?>">
                <i class="fas fa-eye"></i> Vista previa
            </button>
        </div>
        <div class="plantilla-info">
            <h3>
                <span class="plantilla-color" style="background:<?= Security::escapeHtml($p['colorAcento']) ?>;"></span>
                <?= Security::escapeHtml($p['nombre']) ?>
            </h3>
            <p><?= Security::escapeHtml($p['descripcion']) ?></p>
            <button type="button" class="boton-primario" data-aplicar-plantilla="<?= Security::escapeHtml($slug) ?>"
                    data-nombre="<?= Security::escapeHtml($p['nombre']) ?>">
                <i class="fas fa-wand-magic-sparkles"></i> Aplicar plantilla
            </button>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<!-- Modal de confirmación de aplicación de plantilla -->
<div class="plantilla-modal" id="plantilla-modal">
    <div class="plantilla-modal-caja panel">
        <h3><i class="fas fa-triangle-exclamation" style="color:var(--accent);"></i> Aplicar plantilla</h3>
        <p>Vas a aplicar la plantilla <strong id="plantilla-modal-nombre"></strong>.
           Esto <strong>reemplazará todas las secciones del borrador actual</strong> con las de la plantilla.
           La web publicada no cambiará hasta que pulses «Publicar».</p>
        <form method="POST" action="../../../../controladores/admin/landing/aplicar_plantilla.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="plantilla" id="plantilla-modal-slug" value="">
            <div class="plantilla-modal-botones">
                <button type="button" class="boton-secundario" id="plantilla-modal-cancelar">Cancelar</button>
                <button type="submit" class="boton-primario">Sí, aplicar plantilla</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de previsualización iframe -->
<div class="plantilla-preview-modal" id="preview-modal">
    <div class="plantilla-preview-contenedor">
        <div class="plantilla-preview-toolbar">
            <span id="preview-nombre-label"><i class="fas fa-eye"></i> Previsualización</span>
            <div class="plantilla-preview-btns-device">
                <button type="button" class="preview-device-btn active" data-device="desktop" title="Escritorio">
                    <i class="fas fa-desktop"></i>
                </button>
                <button type="button" class="preview-device-btn" data-device="tablet" title="Tablet">
                    <i class="fas fa-tablet-screen-button"></i>
                </button>
                <button type="button" class="preview-device-btn" data-device="mobile" title="Móvil">
                    <i class="fas fa-mobile-screen-button"></i>
                </button>
            </div>
            <button type="button" class="boton-secundario" id="preview-modal-close" style="padding:6px 14px;font-size:0.85rem;">
                <i class="fas fa-xmark"></i> Cerrar
            </button>
        </div>
        <div class="plantilla-preview-frame-wrap" id="preview-frame-wrap">
            <iframe id="preview-iframe" src="" frameborder="0" title="Previsualización de plantilla"></iframe>
        </div>
        <div class="plantilla-preview-loading" id="preview-loading">
            <i class="fas fa-spinner fa-spin"></i> Cargando previsualización…
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../vistas/admin/comunes/footer.php'; ?>

<style>
/* ── Grid de plantillas ─────────────────────────────────────────────── */
.plantillas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
.plantilla-card { padding: 0; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s, box-shadow 0.2s; }
.plantilla-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.15); }
.plantilla-actual { outline: 2px solid var(--accent); }
.plantilla-thumb { position: relative; border-bottom: 1px solid var(--border); background: var(--surface-2); overflow: hidden; }
.plantilla-thumb img { width: 100%; display: block; aspect-ratio: 10 / 7; object-fit: cover; transition: transform 0.35s ease; }
.plantilla-card:hover .plantilla-thumb img { transform: scale(1.03); }
.plantilla-badge { position: absolute; top: 12px; right: 12px; }

/* Botón de preview sobre la thumbnail */
.plantilla-preview-btn {
    position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%) translateY(8px);
    background: rgba(0,0,0,.7); color: #fff; border: none; border-radius: 20px;
    padding: 7px 16px; font-size: 0.8rem; cursor: pointer; opacity: 0;
    transition: opacity 0.25s, transform 0.25s; white-space: nowrap;
    display: flex; align-items: center; gap: 6px;
}
.plantilla-thumb:hover .plantilla-preview-btn { opacity: 1; transform: translateX(-50%) translateY(0); }

.plantilla-info { padding: 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
.plantilla-info h3 { display: flex; align-items: center; gap: 10px; font-size: 17px; margin: 0; color: var(--text); }
.plantilla-color { width: 16px; height: 16px; border-radius: 5px; flex: none; }
.plantilla-info p { color: var(--dim); font-size: 13.5px; margin: 0; flex: 1; }
.plantilla-info .boton-primario { align-self: flex-start; }

/* ── Modal de confirmación ──────────────────────────────────────────── */
.plantilla-modal {
    display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,.5);
    align-items: center; justify-content: center; padding: 20px;
}
.plantilla-modal.abierto { display: flex; }
.plantilla-modal-caja { max-width: 460px; width: 100%; }
.plantilla-modal-caja h3 { display: flex; gap: 10px; align-items: center; margin-top: 0; color: var(--text); }
.plantilla-modal-caja p { color: var(--dim); font-size: 14px; }
.plantilla-modal-botones { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; }

/* ── Modal de previsualización ──────────────────────────────────────── */
.plantilla-preview-modal {
    display: none; position: fixed; inset: 0; z-index: 2000;
    background: rgba(0,0,0,.85); align-items: center; justify-content: center;
    padding: 16px; animation: fadeInPreview 0.2s ease;
}
.plantilla-preview-modal.abierto { display: flex; }
@keyframes fadeInPreview { from { opacity: 0; } to { opacity: 1; } }

.plantilla-preview-contenedor {
    display: flex; flex-direction: column;
    width: 100%; max-width: 1200px; height: 90vh;
    border-radius: 14px; overflow: hidden;
    background: var(--bg-panel, #1e1e2e); box-shadow: 0 24px 60px rgba(0,0,0,.5);
}
.plantilla-preview-toolbar {
    display: flex; align-items: center; gap: 12px; padding: 10px 16px;
    background: var(--bg-body); border-bottom: 1px solid var(--border);
    flex-shrink: 0;
}
#preview-nombre-label { font-size: 0.9rem; color: var(--dim); flex: 1; display: flex; align-items: center; gap: 8px; }

.plantilla-preview-btns-device { display: flex; gap: 4px; }
.preview-device-btn {
    padding: 5px 10px; border-radius: 6px; border: 1px solid var(--border);
    background: transparent; color: var(--dim); cursor: pointer; transition: all 0.2s;
}
.preview-device-btn.active, .preview-device-btn:hover {
    background: var(--accent); color: #fff; border-color: var(--accent);
}

.plantilla-preview-frame-wrap {
    flex: 1; overflow: hidden; display: flex; align-items: flex-start; justify-content: center;
    background: #e5e7eb; padding: 0; transition: padding 0.3s;
}
.plantilla-preview-frame-wrap.device-tablet { padding: 12px 0; }
.plantilla-preview-frame-wrap.device-mobile { padding: 20px 0; }

#preview-iframe {
    width: 100%; height: 100%; border: none;
    transition: width 0.3s ease, box-shadow 0.3s;
}
.plantilla-preview-frame-wrap.device-tablet #preview-iframe {
    width: 768px; box-shadow: 0 0 20px rgba(0,0,0,.3); border-radius: 4px;
}
.plantilla-preview-frame-wrap.device-mobile #preview-iframe {
    width: 390px; box-shadow: 0 0 20px rgba(0,0,0,.3); border-radius: 12px;
}

.plantilla-preview-loading {
    display: none; position: absolute; inset: 0;
    align-items: center; justify-content: center;
    color: #fff; font-size: 1.1rem; gap: 10px; flex-direction: column;
    background: rgba(0,0,0,.6); z-index: 10;
}
.plantilla-preview-loading.visible { display: flex; }
</style>

<script>
(function () {
    var $modal      = document.getElementById('plantilla-modal');
    var $previewMod = document.getElementById('preview-modal');
    var $iframe     = document.getElementById('preview-iframe');
    var $frameWrap  = document.getElementById('preview-frame-wrap');
    var $loading    = document.getElementById('preview-loading');
    var $previewLbl = document.getElementById('preview-nombre-label');

    /* ── Confirmación de aplicar plantilla ── */
    document.querySelectorAll('[data-aplicar-plantilla]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('plantilla-modal-slug').value = this.dataset.aplicarPlantilla;
            document.getElementById('plantilla-modal-nombre').textContent = this.dataset.nombre;
            $modal.classList.add('abierto');
        });
    });
    document.getElementById('plantilla-modal-cancelar').addEventListener('click', function () {
        $modal.classList.remove('abierto');
    });
    $modal.addEventListener('click', function (e) { if (e.target === this) this.classList.remove('abierto'); });

    /* ── Previsualización ── */
    function openPreview(slug, nombre, url) {
        $iframe.src = '';
        $previewLbl.innerHTML = '<i class="fas fa-eye"></i> Previsualización — <strong>' + nombre + '</strong>';
        $loading.classList.add('visible');
        $previewMod.classList.add('abierto');
        // Pequeño delay para que aparezca la animación antes del iframe
        setTimeout(function () {
            $iframe.src = url;
        }, 80);
    }

    $iframe.addEventListener('load', function () { $loading.classList.remove('visible'); });

    document.querySelectorAll('.plantilla-preview-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var card = this.closest('[data-slug]');
            var slug = card.dataset.slug;
            var nombre = card.querySelector('h3').textContent.trim();
            openPreview(slug, nombre, this.dataset.previewUrl);
        });
    });

    document.getElementById('preview-modal-close').addEventListener('click', function () {
        $previewMod.classList.remove('abierto');
        $iframe.src = '';
    });
    $previewMod.addEventListener('click', function (e) {
        if (e.target === this) { this.classList.remove('abierto'); $iframe.src = ''; }
    });

    /* ── Device switcher ── */
    document.querySelectorAll('.preview-device-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.preview-device-btn').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
            $frameWrap.className = 'plantilla-preview-frame-wrap device-' + this.dataset.device;
        });
    });

    /* ── ESC to close ── */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            $previewMod.classList.remove('abierto');
            $modal.classList.remove('abierto');
            $iframe.src = '';
        }
    });
}());
</script>


<div class="cabecera">
    <h1><i class="fas fa-palette"></i> Plantillas de la web pública</h1>
    <div class="acciones-pagina">
        <a href="builder.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver al constructor</a>
    </div>
</div>

<div class="panel margen-abajo">
    <p class="texto-suave" style="margin:0;">
        Elige una plantilla como punto de partida. Al aplicarla se <strong>reemplaza el borrador actual</strong>
        (la web publicada no cambia hasta que pulses «Publicar» en el constructor). Después podrás editar
        todos los textos e imágenes, y añadir, quitar o reordenar secciones.
    </p>
</div>

<div class="plantillas-grid">
    <?php foreach ($plantillas as $slug => $p):
        $esActual = ($landingCfg['plantilla'] === $slug); ?>
    <article class="panel plantilla-card<?= $esActual ? ' plantilla-actual' : '' ?>">
        <div class="plantilla-thumb">
            <img src="/public/imagenes/landing/<?= Security::escapeHtml($p['thumbnail']) ?>" alt="<?= Security::escapeHtml($p['nombre']) ?>">
            <?php if ($esActual): ?>
            <span class="texto-estado verde plantilla-badge">Plantilla del borrador</span>
            <?php endif; ?>
        </div>
        <div class="plantilla-info">
            <h3>
                <span class="plantilla-color" style="background:<?= Security::escapeHtml($p['colorAcento']) ?>;"></span>
                <?= Security::escapeHtml($p['nombre']) ?>
            </h3>
            <p><?= Security::escapeHtml($p['descripcion']) ?></p>
            <button type="button" class="boton-primario" data-aplicar-plantilla="<?= Security::escapeHtml($slug) ?>"
                    data-nombre="<?= Security::escapeHtml($p['nombre']) ?>">
                <i class="fas fa-wand-magic-sparkles"></i> Aplicar plantilla
            </button>
        </div>
    </article>
    <?php endforeach; ?>
</div>

<!-- Modal de confirmación de aplicación de plantilla -->
<div class="plantilla-modal" id="plantilla-modal">
    <div class="plantilla-modal-caja panel">
        <h3><i class="fas fa-triangle-exclamation" style="color:var(--accent);"></i> Aplicar plantilla</h3>
        <p>Vas a aplicar la plantilla <strong id="plantilla-modal-nombre"></strong>.
           Esto <strong>reemplazará todas las secciones del borrador actual</strong> con las de la plantilla.
           La web publicada no cambiará hasta que pulses «Publicar».</p>
        <form method="POST" action="../../../controladores/admin/landing/aplicar_plantilla.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="plantilla" id="plantilla-modal-slug" value="">
            <div class="plantilla-modal-botones">
                <button type="button" class="boton-secundario" id="plantilla-modal-cancelar">Cancelar</button>
                <button type="submit" class="boton-primario">Sí, aplicar plantilla</button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

<style>
.plantillas-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.plantilla-card { padding: 0; overflow: hidden; display: flex; flex-direction: column; }
.plantilla-actual { outline: 2px solid var(--accent); }
.plantilla-thumb { position: relative; border-bottom: 1px solid var(--border); background: var(--surface-2); }
.plantilla-thumb img { width: 100%; display: block; aspect-ratio: 10 / 7; object-fit: cover; }
.plantilla-badge { position: absolute; top: 12px; right: 12px; }
.plantilla-info { padding: 20px; display: flex; flex-direction: column; gap: 10px; flex: 1; }
.plantilla-info h3 { display: flex; align-items: center; gap: 10px; font-size: 17px; margin: 0; color: var(--text); }
.plantilla-color { width: 16px; height: 16px; border-radius: 5px; flex: none; }
.plantilla-info p { color: var(--dim); font-size: 13.5px; margin: 0; flex: 1; }
.plantilla-info .boton-primario { align-self: flex-start; }
.plantilla-modal {
    display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,.5);
    align-items: center; justify-content: center; padding: 20px;
}
.plantilla-modal.abierto { display: flex; }
.plantilla-modal-caja { max-width: 460px; width: 100%; }
.plantilla-modal-caja h3 { display: flex; gap: 10px; align-items: center; margin-top: 0; color: var(--text); }
.plantilla-modal-caja p { color: var(--dim); font-size: 14px; }
.plantilla-modal-botones { display: flex; justify-content: flex-end; gap: 10px; margin-top: 18px; }
</style>

<script>
$(function () {
    var $modal = $('#plantilla-modal');
    $('[data-aplicar-plantilla]').on('click', function () {
        $('#plantilla-modal-slug').val($(this).data('aplicar-plantilla'));
        $('#plantilla-modal-nombre').text($(this).data('nombre'));
        $modal.addClass('abierto');
    });
    $('#plantilla-modal-cancelar').on('click', function () { $modal.removeClass('abierto'); });
    $modal.on('click', function (e) { if (e.target === this) $modal.removeClass('abierto'); });
});
</script>

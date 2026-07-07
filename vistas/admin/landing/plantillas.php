<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_landing');

require_once __DIR__ . "/../../../modelos/landing.php";
require_once __DIR__ . "/../../../include/landing/plantillas.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$landingCfg = obtenerLandingConfig();
if (empty($landingCfg['plantilla'])) {
    header("Location: onboarding.php");
    exit;
}
$plantillas = landing_plantillas();

$titulo_pagina = "AULAPRO | PLANTILLAS DE LA WEB";
$seccion       = 'landing';
include_once __DIR__ . '/../comunes/nav.php';
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

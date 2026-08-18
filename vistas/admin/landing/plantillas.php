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

$titulo_pagina = "Plantillas de la Web";
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
    <?php foreach ($plantillas as $slug => $plantilla):
        $esActual = ($landingCfg['plantilla'] === $slug);
        $sinTema  = !file_exists(__DIR__ . '/../../../landing-system/temas/tema-' . $slug . '.css'); ?>
    <article class="panel plantilla-card<?= $esActual ? ' plantilla-actual' : '' ?>">
        <div class="plantilla-thumb">
            <img src="../../../public/imagenes/landing/<?= Security::escapeHtml($plantilla['thumbnail']) ?>" alt="<?= Security::escapeHtml($plantilla['nombre']) ?>">
            <?php if ($esActual): ?>
            <span class="texto-estado verde plantilla-badge">Plantilla del borrador</span>
            <?php endif; ?>
            <?php if ($sinTema): ?>
            <span class="texto-estado rojo plantilla-badge plantilla-badge-izquierda"
                  title="Falta landing-system/temas/tema-<?= Security::escapeHtml($slug) ?>.css — la plantilla funciona pero se ve con el aspecto genérico de base.css, sin su identidad visual propia.">
                <i class="fas fa-triangle-exclamation"></i> Sin hoja de estilos
            </span>
            <?php endif; ?>
        </div>
        <div class="plantilla-info">
            <h3>
                <span class="plantilla-color" style="background:<?= Security::escapeHtml($plantilla['colorAcento']) ?>;"></span>
                <?= Security::escapeHtml($plantilla['nombre']) ?>
            </h3>
            <p><?= Security::escapeHtml($plantilla['descripcion']) ?></p>
            <button type="button" class="boton-primario" data-aplicar-plantilla="<?= Security::escapeHtml($slug) ?>"
                    data-nombre="<?= Security::escapeHtml($plantilla['nombre']) ?>" style="width: 100%; justify-content: center; margin-top: 10px;">
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
.plantillas-grid { 
    display: grid; 
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
    gap: 24px; 
}
.plantilla-card { 
    padding: 0; 
    overflow: hidden; 
    display: flex; 
    flex-direction: column;
    border-radius: 16px;
    border: 1px solid var(--border);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.3s ease;
    background: var(--bg-panel);
}
.plantilla-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px -8px rgba(0,0,0,0.15);
    border-color: var(--accent);
}
.plantilla-actual { 
    outline: 2px solid var(--accent); 
    box-shadow: 0 0 0 4px rgba(var(--accent-rgb), 0.15);
}
.plantilla-thumb { 
    position: relative; 
    border-bottom: 1px solid var(--border); 
    background: var(--surface-2); 
    overflow: hidden;
}
.plantilla-thumb img { 
    width: 100%; 
    display: block; 
    aspect-ratio: 16 / 10; 
    object-fit: cover; 
    transition: transform 0.5s ease;
}
.plantilla-card:hover .plantilla-thumb img {
    transform: scale(1.05);
}
.plantilla-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: var(--green-bg, #dcfce7);
    color: var(--green-fg, #166534);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    backdrop-filter: blur(4px);
}
.plantilla-badge-izquierda {
    right: auto;
    left: 16px;
}
.plantilla-info { 
    padding: 24px; 
    display: flex; 
    flex-direction: column; 
    gap: 12px; 
    flex: 1; 
}
.plantilla-info h3 { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    font-size: 18px; 
    margin: 0; 
    color: var(--text); 
    font-weight: 700;
}
.plantilla-color { 
    width: 18px; 
    height: 18px; 
    border-radius: 6px; 
    flex: none; 
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.1);
}
.plantilla-info p { 
    color: var(--dim); 
    font-size: 14px; 
    margin: 0; 
    flex: 1; 
    line-height: 1.5;
}
.plantilla-info .boton-primario { 
    align-self: stretch; 
    transition: all 0.2s ease;
}
.plantilla-modal {
    display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,.5);
    align-items: center; justify-content: center; padding: 20px;
    backdrop-filter: blur(3px);
    opacity: 0; transition: opacity 0.3s ease;
}
.plantilla-modal.abierto { display: flex; opacity: 1; }
.plantilla-modal-caja { 
    max-width: 480px; 
    width: 100%; 
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    transform: scale(0.95); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.plantilla-modal.abierto .plantilla-modal-caja {
    transform: scale(1);
}
.plantilla-modal-caja h3 { display: flex; gap: 10px; align-items: center; margin-top: 0; color: var(--text); font-size: 20px; }
.plantilla-modal-caja p { color: var(--dim); font-size: 14.5px; line-height: 1.5; }
.plantilla-modal-botones { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
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

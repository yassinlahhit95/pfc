<?php
require_once __DIR__ . '/../../../include/Security.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . '/../../../modelos/configuracion.php';
$cfg = obtenerConfiguracionCentro();

$titulo_pagina = "AULAPRO | CONFIGURACIÓN DEL CENTRO";
$seccion = 'configuracion';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>CONFIGURACIÓN DEL CENTRO</h1>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php endif; ?>

<div class="panel margen-abajo">
    <h3 class="panel-titulo">Módulos y Funcionalidades</h3>
    <p class="texto-suave mb-4">Habilita o deshabilita módulos del sistema en tiempo real.</p>
    
    <div class="feature-toggle-grid">
        <div class="feature-card">
            <div class="feature-info">
                <i class="fas fa-user-plus feature-icon" style="color: #4f46e5;"></i>
                <div>
                    <div class="feature-label">Pre-matrícula</div>
                    <div class="feature-desc">Habilita el portal de admisión pública</div>
                </div>
            </div>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_prematricula" <?= ($cfg['feature_prematricula'] ? 'checked' : '') ?>>
                <span class="slider round"></span>
            </label>
        </div>

        <div class="feature-card">
            <div class="feature-info">
                <i class="fas fa-comments feature-icon" style="color: #10b981;"></i>
                <div>
                    <div class="feature-label">Sistema de Chat</div>
                    <div class="feature-desc">Mensajería instantánea entre usuarios</div>
                </div>
            </div>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_chat" <?= ($cfg['feature_chat'] ? 'checked' : '') ?>>
                <span class="slider round"></span>
            </label>
        </div>

        <div class="feature-card">
            <div class="feature-info">
                <i class="fas fa-boxes feature-icon" style="color: #f59e0b;"></i>
                <div>
                    <div class="feature-label">Inventario</div>
                    <div class="feature-desc">Gestión de recursos y préstamos</div>
                </div>
            </div>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_inventario" <?= ($cfg['feature_inventario'] ? 'checked' : '') ?>>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
</div>

<form method="POST" action="../../../controladores/admin/configuracion/guardar.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

    <div class="panel margen-abajo">
        <h3 class="panel-titulo">Datos del Centro</h3>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Nombre del Centro *</label>
                <input type="text" name="nombreCentro" value="<?= Security::escapeHtml($cfg['nombreCentro']) ?>" required>
            </div>
            <div class="campo">
                <label>Código del Centro</label>
                <input type="text" name="codigoCentro" value="<?= Security::escapeHtml($cfg['codigoCentro']) ?>">
            </div>
            <div class="campo">
                <label>Curso Escolar</label>
                <input type="text" name="cursoEscolar" value="<?= Security::escapeHtml($cfg['cursoEscolar']) ?>" placeholder="2024-2025">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Dirección</label>
                <input type="text" name="direccionCentro" value="<?= Security::escapeHtml($cfg['direccionCentro']) ?>">
            </div>
            <div class="campo">
                <label>Ciudad</label>
                <input type="text" name="ciudadCentro" value="<?= Security::escapeHtml($cfg['ciudadCentro']) ?>">
            </div>
            <div class="campo">
                <label>Código Postal</label>
                <input type="text" name="cpCentro" value="<?= Security::escapeHtml($cfg['cpCentro']) ?>">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Teléfono</label>
                <input type="text" name="telefonoCentro" value="<?= Security::escapeHtml($cfg['telefonoCentro']) ?>">
            </div>
            <div class="campo relleno">
                <label>Email del Centro</label>
                <input type="email" name="emailCentro" value="<?= Security::escapeHtml($cfg['emailCentro']) ?>">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Nombre Director/a Firmante</label>
                <input type="text" name="nombreDirectorFirmante" value="<?= Security::escapeHtml($cfg['nombreDirectorFirmante']) ?>">
            </div>
        </div>
        <div class="caja caja-libre espacio-grande">
            <div class="campo relleno">
                <label>Texto Legal / Pie de Documento</label>
                <textarea name="textoLegal" rows="3"><?= Security::escapeHtml($cfg['textoLegal']) ?></textarea>
            </div>
        </div>
    </div>

    <div class="panel margen-abajo">
        <h3 class="panel-titulo">Logotipos (PNG/JPG, máx. 2MB)</h3>
        <div class="caja caja-libre espacio-grande">

            <?php foreach ([
                'logoCentro'    => 'Logo del Centro (izquierda)',
                'logoGobierno1' => 'Logo Gobierno / Ministerio (derecha)',
                'logoGobierno2' => 'Logo Consejería / Junta (centro-derecha)',
            ] as $field => $label): ?>
            <div class="campo relleno cfg-logo-campo">
                <label><?= $label ?></label>
                <?php if (!empty($cfg[$field])): ?>
                    <img src="../../../public/uploads/configuracion/<?= Security::escapeHtml(basename($cfg[$field])) ?>"
                         alt="logo" class="cfg-logo-preview">
                <?php endif; ?>
                <input type="file" name="<?= $field ?>" accept="image/*">
                <small class="texto-suave">Deja vacío para mantener el actual</small>
            </div>
            <?php endforeach; ?>

        </div>
    </div>

    <div class="acciones">
        <button type="submit" class="boton-primario">
            <i class="fas fa-save"></i> GUARDAR CONFIGURACIÓN
        </button>
    </div>
</form>

<style>
.cfg-logo-preview { display:block; max-height:80px; max-width:200px; object-fit:contain; margin-bottom:8px; border:1px solid #e5e7eb; border-radius:6px; padding:4px; background:#f9fafb; }
.panel-titulo { font-size:.85rem; font-weight:700; letter-spacing:.05em; color:#6b7280; margin-bottom:16px; padding-bottom:8px; border-bottom:1px solid #e5e7eb; }
</style>

<?php include '../comunes/footer.php'; ?>

<style>
.feature-toggle-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px; }
.feature-card { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s; }
.feature-card:hover { border-color: var(--accent); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.feature-info { display: flex; align-items: center; gap: 15px; }
.feature-icon { font-size: 1.25rem; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 10px; }
.feature-label { font-weight: 700; font-size: 0.95rem; color: #111827; }
.feature-desc { font-size: 0.8rem; color: #6b7280; }

/* Switch Style */
.switch { position: relative; display: inline-block; width: 46px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(22px); }
</style>

<script>
$(document).ready(function() {
    $('.toggle-feature').on('change', function() {
        const feature = $(this).data('feature');
        const estado = $(this).is(':checked') ? 1 : 0;
        const $card = $(this).closest('.feature-card');

        $card.css('opacity', '0.6').css('pointer-events', 'none');

        $.ajax({
            url: '../../../controladores/admin/configuracion/toggle_feature.php',
            type: 'POST',
            data: { 
                feature: feature, 
                estado: estado,
                csrf_token: '<?= Security::generateCSRFToken() ?>'
            },
            success: function(res) {
                $card.css('opacity', '1').css('pointer-events', 'all');
                if (res.status === 'success') {
                    if (window.Toast) Toast.show(res.message, 'ok');
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                $card.css('opacity', '1').css('pointer-events', 'all');
                alert('Error de conexión');
            }
        });
    });
});
</script>

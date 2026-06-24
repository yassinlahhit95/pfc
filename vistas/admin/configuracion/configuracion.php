<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../include/form_helpers.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

// Restore submitted values on validation error
$datos = $_SESSION['datos_configuracion'] ?? [];
unset($_SESSION['datos_configuracion']);

require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../modelos/directores.php';
$cfg         = obtenerConfiguracionCentro();
$adminActual = obtenerDirectorPorId((int)$_SESSION['idAdmin']);
$mfaActivo   = !empty($adminActual['mfa_enabled']);
$saasLocked  = FeatureGuard::isLocked();

$titulo_pagina = "AULAPRO | CONFIGURACIÓN DEL CENTRO";
$seccion = 'configuracion';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <div>
        <h1><i class="fas fa-cog" style="margin-right:8px;opacity:.7;"></i>Configuración del Centro</h1>
        <p class="subtitulo-encabezado">Ajusta los datos, logotipos y módulos de tu instancia</p>
    </div>
    <div class="acciones-pagina">
        <button type="button" class="boton-primario" onclick="var f=document.getElementById('form-configuracion');f.requestSubmit?f.requestSubmit():f.submit()">
            <i class="fas fa-save"></i> Guardar cambios
        </button>
    </div>
</div>

<!-- ── Módulos y Funcionalidades ─────────────────────────────────────── -->
<div class="panel margen-abajo">
    <div class="panel-titulo-seccion">
        <i class="fas fa-puzzle-piece"></i> Módulos y Funcionalidades
    </div>

    <?php if ($saasLocked): ?>
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;margin-bottom:16px;background:var(--surface-2);border:1px solid var(--border);border-radius:10px;font-size:.87rem;color:var(--text);">
        <i class="fas fa-lock" style="color:var(--accent);font-size:1rem;flex-shrink:0;"></i>
        <span><strong>Control bloqueado por la plataforma SaaS.</strong> Estos módulos están siendo gestionados centralmente y no se pueden modificar desde aquí.
        <a href="../saas/estado.php" style="color:var(--accent);font-weight:700;margin-left:6px;">Ver estado →</a></span>
    </div>
    <?php else: ?>
    <p class="texto-suave" style="margin-bottom:16px;">Habilita o deshabilita módulos del sistema en tiempo real.</p>
    <?php endif; ?>

    <div class="feature-toggle-grid">
        <?php
        $features = [
            ['key' => 'feature_prematricula', 'icon' => 'fa-user-plus',     'color' => '#4f46e5', 'label' => 'Pre-matrícula',    'desc' => 'Portal de admisión pública'],
            ['key' => 'feature_chat',         'icon' => 'fa-comments',      'color' => '#10b981', 'label' => 'Chat',              'desc' => 'Mensajería instantánea'],
            ['key' => 'feature_inventario',   'icon' => 'fa-boxes',         'color' => '#f59e0b', 'label' => 'Inventario',        'desc' => 'Recursos y préstamos'],
            ['key' => 'feature_subida_tfg',   'icon' => 'fa-file-upload',   'color' => '#8b5cf6', 'label' => 'Entrega de TFG',   'desc' => 'Subida del trabajo fin de grado'],
            ['key' => 'feature_anuncios',     'icon' => 'fa-bullhorn',      'color' => '#f43f5e', 'label' => 'Anuncios',          'desc' => 'Tablón de avisos del centro'],
            ['key' => 'feature_eventos',      'icon' => 'fa-calendar-days', 'color' => '#0ea5e9', 'label' => 'Eventos',           'desc' => 'Calendario de actividades'],
            ['key' => 'feature_retos',        'icon' => 'fa-trophy',        'color' => '#f59e0b', 'label' => 'Retos',             'desc' => 'Actividades académicas gamificadas'],
            ['key' => 'feature_mensajes',     'icon' => 'fa-envelope',      'color' => '#6366f1', 'label' => 'Mensajería',        'desc' => 'Reclamaciones y mensajes internos'],
            ['key' => 'feature_pagos',        'icon' => 'fa-credit-card',   'color' => '#10b981', 'label' => 'Pagos',             'desc' => 'Gestión de pagos y matrículas'],
            ['key' => 'feature_gastos',       'icon' => 'fa-receipt',       'color' => '#ef4444', 'label' => 'Gastos',            'desc' => 'Control de gastos del centro'],
            ['key' => 'feature_informes',     'icon' => 'fa-file-pdf',      'color' => '#64748b', 'label' => 'Informes PDF',      'desc' => 'Boletines, listados y horarios'],
            ['key' => 'feature_horario',      'icon' => 'fa-calendar-alt',  'color' => '#4f46e5', 'label' => 'Cuadro Horario',    'desc' => 'Horarios y asignaciones de clase'],
            ['key' => 'feature_geoblock_admin','icon' => 'fa-globe-europe','color' => '#dc2626', 'label' => 'Geo-Block (España)','desc' => 'Bloquea el panel admin al extranjero'],
        ];
        foreach ($features as $feat):
            $val = $cfg[$feat['key']] ?? 1;
        ?>
        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas <?= $feat['icon'] ?> feature-icon" style="color:<?= $feat['color'] ?>;"></i>
                <div>
                    <div class="feature-label"><?= $feat['label'] ?></div>
                    <div class="feature-desc"><?= $feat['desc'] ?></div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= $val ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="feature-switch">
                <input type="checkbox" class="toggle-feature" data-feature="<?= $feat['key'] ?>" <?= $val ? 'checked' : '' ?>>
                <span class="feature-track"></span>
            </label>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ── Seguridad de la cuenta ────────────────────────────────────────── -->
<div class="panel margen-abajo">
    <div class="panel-titulo-seccion">
        <i class="fas fa-shield-alt"></i> Seguridad de la Cuenta
    </div>
    <div class="feature-card" style="max-width:580px;">
        <div class="feature-info">
            <i class="fas fa-shield-alt feature-icon" style="color:<?= $mfaActivo ? '#10b981' : 'var(--mut)' ?>;"></i>
            <div>
                <div class="feature-label">Verificación en dos pasos (2FA)</div>
                <div class="feature-desc">
                    <?= $mfaActivo
                        ? 'Activa — tu cuenta está protegida con un autenticador TOTP.'
                        : 'Inactiva — añade una segunda capa de seguridad a tu cuenta.' ?>
                </div>
            </div>
        </div>
        <?php if ($mfaActivo): ?>
            <span style="display:inline-flex;align-items:center;gap:6px;color:#10b981;font-weight:600;font-size:.85rem;">
                <i class="fas fa-check-circle"></i> Activada
            </span>
        <?php else: ?>
            <a href="../../auth/mfa_configurar.php" class="boton-secundario" style="white-space:nowrap;font-size:.85rem;padding:6px 14px;">
                <i class="fas fa-lock"></i> Activar 2FA
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════════════
     FORM: Datos del Centro + Logotipos
     ══════════════════════════════════════════════════════════════════ -->
<form id="form-configuracion" method="POST"
      action="../../../controladores/admin/configuracion/guardar.php"
      enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

    <!-- ── Datos del Centro ───────────────────────────────────────── -->
    <div class="panel margen-abajo">
        <div class="panel-titulo-seccion">
            <i class="fas fa-building"></i> Datos del Centro
        </div>

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'nombreCentro') ?>">
                <label for="nombreCentro">Nombre del Centro *</label>
                <input type="text" id="nombreCentro" name="nombreCentro"
                       value="<?= Security::escapeHtml($datos['nombreCentro'] ?? $cfg['nombreCentro']) ?>">
                <?= fieldError($errores, 'nombreCentro') ?>
            </div>
            <div class="campo">
                <label for="codigoCentro">Código del Centro</label>
                <input type="text" id="codigoCentro" name="codigoCentro"
                       value="<?= Security::escapeHtml($datos['codigoCentro'] ?? $cfg['codigoCentro']) ?>"
                       placeholder="Ej: 28001234">
            </div>
            <div class="campo">
                <label for="cursoEscolar">Curso Escolar</label>
                <input type="text" id="cursoEscolar" name="cursoEscolar"
                       value="<?= Security::escapeHtml($datos['cursoEscolar'] ?? $cfg['cursoEscolar']) ?>"
                       placeholder="2024-2025">
            </div>
            <div class="campo ancho-total">
                <label for="direccionCentro">Dirección</label>
                <input type="text" id="direccionCentro" name="direccionCentro"
                       value="<?= Security::escapeHtml($datos['direccionCentro'] ?? $cfg['direccionCentro']) ?>"
                       placeholder="Calle, número, etc.">
            </div>
            <div class="campo">
                <label for="ciudadCentro">Ciudad</label>
                <input type="text" id="ciudadCentro" name="ciudadCentro"
                       value="<?= Security::escapeHtml($datos['ciudadCentro'] ?? $cfg['ciudadCentro']) ?>">
            </div>
            <div class="campo">
                <label for="cpCentro">Código Postal</label>
                <input type="text" id="cpCentro" name="cpCentro"
                       value="<?= Security::escapeHtml($datos['cpCentro'] ?? $cfg['cpCentro']) ?>"
                       placeholder="28001">
            </div>
            <div class="campo<?= fieldClass($errores, 'telefonoCentro') ?>">
                <label for="telefonoCentro">Teléfono</label>
                <input type="tel" id="telefonoCentro" name="telefonoCentro"
                       value="<?= Security::escapeHtml($datos['telefonoCentro'] ?? $cfg['telefonoCentro']) ?>"
                       placeholder="912 345 678">
                <?= fieldError($errores, 'telefonoCentro') ?>
            </div>
            <div class="campo<?= fieldClass($errores, 'emailCentro') ?>">
                <label for="emailCentro">Email del Centro</label>
                <input type="email" id="emailCentro" name="emailCentro"
                       value="<?= Security::escapeHtml($datos['emailCentro'] ?? $cfg['emailCentro']) ?>"
                       placeholder="info@centro.es">
                <?= fieldError($errores, 'emailCentro') ?>
            </div>
            <div class="campo ancho-total">
                <label for="nombreDirectorFirmante">Nombre del Director/a Firmante</label>
                <input type="text" id="nombreDirectorFirmante" name="nombreDirectorFirmante"
                       value="<?= Security::escapeHtml($datos['nombreDirectorFirmante'] ?? $cfg['nombreDirectorFirmante']) ?>"
                       placeholder="Para boletines y documentos oficiales">
            </div>
            <div class="campo ancho-total">
                <label for="textoLegal">Texto Legal / Pie de Documento</label>
                <textarea id="textoLegal" name="textoLegal" rows="3" maxlength="1000"><?= Security::escapeHtml($datos['textoLegal'] ?? $cfg['textoLegal']) ?></textarea>
                <small class="texto-suave">Aparece en el pie de boletines y otros documentos PDF · máx. 1000 caracteres</small>
            </div>
        </div>
    </div>

    <!-- ── Logotipos ─────────────────────────────────────────────── -->
    <div class="panel margen-abajo">
        <div class="panel-titulo-seccion">
            <i class="fas fa-image"></i> Logotipos
        </div>
        <p class="texto-suave" style="margin-bottom:20px;">
            Se usan en los PDF (boletines, horarios) y en la cabecera de documentos oficiales. PNG, JPG o WEBP · máx. 2&nbsp;MB · se recomienda 200&nbsp;×&nbsp;80&nbsp;px en fondo blanco.
        </p>

        <div class="formulario">
            <?php
            $logoConfig = [
                'logoCentro'    => ['label' => 'Logo del Centro',                'desc' => 'Aparece en la parte izquierda de los documentos PDF'],
                'logoGobierno1' => ['label' => 'Logo Gobierno / Ministerio',     'desc' => 'Aparece en la parte derecha de los documentos PDF'],
                'logoGobierno2' => ['label' => 'Logo Consejería / Comunidad',    'desc' => 'Aparece como logo secundario en documentos con doble logo'],
            ];
            foreach ($logoConfig as $field => $meta):
                $tieneActual = !empty($cfg[$field]);
            ?>
            <div class="campo">
                <label><?= $meta['label'] ?></label>

                <!-- Current logo preview -->
                <?php if ($tieneActual): ?>
                <div class="logo-actual" id="logo-actual-<?= $field ?>">
                    <img src="/public/uploads/configuracion/<?= Security::escapeHtml(basename($cfg[$field])) ?>"
                         alt="<?= $meta['label'] ?>" class="logo-preview-img">
                    <button type="button" class="boton-peligro btn-xs"
                            onclick="marcarBorrarLogo('<?= $field ?>')">
                        <i class="fas fa-trash"></i> Eliminar logo
                    </button>
                </div>
                <?php endif; ?>

                <!-- Hidden flag: sent as "1" when user clicks delete -->
                <input type="hidden" name="borrar_<?= $field ?>" id="borrar_<?= $field ?>" value="">

                <!-- Pending delete notice (hidden until user clicks delete) -->
                <div id="pendiente-<?= $field ?>" class="logo-pendiente-borrar" style="display:none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Este logo se eliminará al guardar.
                    <button type="button" class="enlace-boton" onclick="deshacerBorrar('<?= $field ?>')">
                        Deshacer
                    </button>
                </div>

                <!-- File input -->
                <label class="logo-file-label" id="label-file-<?= $field ?>">
                    <i class="fas fa-upload"></i>
                    <?= $tieneActual ? 'Reemplazar logo' : 'Subir logo' ?>
                    <input type="file" name="<?= $field ?>"
                           accept="image/png,image/jpeg,image/webp"
                           onchange="previewLogoSelect(this, '<?= $field ?>')">
                </label>
                <small class="texto-suave" style="margin-top:4px;display:block;"><?= $meta['desc'] ?></small>

                <!-- New file preview (before save) -->
                <div id="new-preview-<?= $field ?>" style="display:none;margin-top:8px;">
                    <img id="new-preview-img-<?= $field ?>" src="" alt="preview" class="logo-preview-img" style="opacity:.8;">
                    <small class="texto-suave" style="display:block;margin-top:4px;">
                        <i class="fas fa-info-circle"></i> Vista previa — se guardará al hacer clic en "Guardar cambios"
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- ── Save button (bottom) ──────────────────────────────────── -->
    <div class="acciones" style="margin-bottom:32px;">
        <button type="submit" class="boton-primario">
            <i class="fas fa-save"></i> Guardar configuración
        </button>
    </div>
</form>

<style>
/* ── Feature toggle grid ─── */
.feature-toggle-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 12px;
}
.feature-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    gap: 12px;
    transition: border-color .18s, box-shadow .18s;
}
.feature-card:hover { border-color: var(--accent); box-shadow: var(--shadow-sm); }
.feature-card-locked { opacity: .72; pointer-events: none; }
.feature-card-locked:hover { border-color: var(--border); box-shadow: none; }
.feature-info { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0; }
.feature-icon {
    width: 38px; height: 38px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface-2); border-radius: 10px;
    font-size: 1.1rem;
}
.feature-label { font-weight: 700; font-size: .9rem; color: var(--text); }
.feature-desc  { font-size: .78rem; color: var(--mut); margin-top: 1px; }
.lock-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px;
    font-size: .78rem; font-weight: 700;
    background: var(--surface-2); color: var(--dim);
    white-space: nowrap; flex-shrink: 0;
}

/* Feature switch (reuse toggle from estilo.css structure but scoped) */
.feature-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
.feature-switch input { opacity: 0; width: 0; height: 0; }
.feature-track {
    position: absolute; inset: 0;
    background: var(--border-2, rgba(15,23,42,.18));
    border-radius: 999px; cursor: pointer; transition: background .2s;
}
.feature-track::before {
    content: '';
    position: absolute; left: 3px; top: 3px;
    width: 18px; height: 18px;
    background: #fff; border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,.25);
    transition: transform .2s;
}
.feature-switch input:checked + .feature-track { background: var(--accent); }
.feature-switch input:checked + .feature-track::before { transform: translateX(20px); }

/* ── Logo upload zone ─── */
.logo-actual {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 14px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-bottom: 10px;
}
.logo-preview-img {
    max-height: 64px;
    max-width: 160px;
    object-fit: contain;
    flex-shrink: 0;
}
.btn-xs {
    padding: 5px 12px;
    font-size: .78rem;
    white-space: nowrap;
}
.logo-pendiente-borrar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    font-size: .83rem;
    color: var(--rojo, #dc2626);
    margin-bottom: 10px;
}
.logo-pendiente-borrar .enlace-boton {
    background: none;
    border: none;
    color: var(--text);
    text-decoration: underline;
    cursor: pointer;
    font-size: .83rem;
    padding: 0;
    margin-left: 4px;
}
.logo-file-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border: 1.5px dashed var(--border-2);
    border-radius: 8px;
    font-size: .85rem;
    font-weight: 600;
    color: var(--dim);
    cursor: pointer;
    transition: border-color .18s, color .18s, background .18s;
    background: var(--surface);
}
.logo-file-label:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--surface-2);
}
.logo-file-label input[type="file"] { display: none; }
</style>

<?php include '../comunes/footer.php'; ?>
<script>
// ── Feature toggles ────────────────────────────────────────────────────
var saasLocked = <?= $saasLocked ? 'true' : 'false' ?>;
var csrfToken  = '<?= Security::generateCSRFToken() ?>';

$(document).ready(function () {
    if (saasLocked) {
        $('.toggle-feature').prop('disabled', true);
        return;
    }
    $('.toggle-feature').on('change', function () {
        var feature  = $(this).data('feature');
        var estado   = $(this).is(':checked') ? 1 : 0;
        var $toggle  = $(this);
        var $card    = $(this).closest('.feature-card');

        $card.css({ opacity: '.6', pointerEvents: 'none' });

        $.ajax({
            url: '../../../controladores/admin/configuracion/toggle_feature.php',
            type: 'POST',
            dataType: 'json',
            data: { feature: feature, estado: estado, csrf_token: csrfToken },
            success: function (res) {
                $card.css({ opacity: '1', pointerEvents: 'all' });
                if (res && res.new_csrf) {
                    csrfToken = res.new_csrf;
                    $('input[name="csrf_token"]').val(res.new_csrf);
                }
                if (!res || res.status !== 'success') {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                    var msg = (res && (res.message || res.msg)) || 'Error al actualizar el módulo.';
                    if (window.Toast) Toast.show(msg, 'error');
                } else {
                    if (window.Toast) Toast.show(res.message || 'Módulo actualizado.', 'success');
                }
            },
            error: function (xhr) {
                $card.css({ opacity: '1', pointerEvents: 'all' });
                $toggle.prop('checked', !$toggle.prop('checked'));
                var msg = xhr.status === 500
                    ? 'Error del servidor (500). Es posible que falte una columna en la BD.'
                    : 'Error de conexión (' + xhr.status + ').';
                if (window.Toast) Toast.show(msg, 'error');
            }
        });
    });
});

// ── Logo delete / undo ─────────────────────────────────────────────────
function marcarBorrarLogo(field) {
    document.getElementById('borrar_' + field).value = '1';
    var actual = document.getElementById('logo-actual-' + field);
    if (actual) actual.style.display = 'none';
    document.getElementById('pendiente-' + field).style.display = 'flex';
}
function deshacerBorrar(field) {
    document.getElementById('borrar_' + field).value = '';
    var actual = document.getElementById('logo-actual-' + field);
    if (actual) actual.style.display = 'flex';
    document.getElementById('pendiente-' + field).style.display = 'none';
}

// ── Logo file preview before upload ───────────────────────────────────
function previewLogoSelect(input, field) {
    var wrap = document.getElementById('new-preview-' + field);
    var img  = document.getElementById('new-preview-img-' + field);
    if (!input.files || !input.files[0]) { wrap.style.display = 'none'; return; }
    var reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
        wrap.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// ── Restore logo delete pending state after failed validation ──────────
document.addEventListener('DOMContentLoaded', function () {
    <?php foreach (['logoCentro','logoGobierno1','logoGobierno2'] as $f): ?>
    <?php if (!empty($datos['borrar_' . $f])): ?>
    marcarBorrarLogo('<?= $f ?>');
    <?php endif; ?>
    <?php endforeach; ?>
});
</script>

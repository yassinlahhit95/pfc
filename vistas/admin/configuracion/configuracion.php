<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

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
    <h1>CONFIGURACIÓN DEL CENTRO</h1>
    <div class="cabecera-acciones">
        <button type="button" class="boton-primario" onclick="var f=document.getElementById('form-configuracion');f.requestSubmit?f.requestSubmit():f.submit()">
            <i class="fas fa-save"></i> Actualizar
        </button>
    </div>
</div>


<div class="panel margen-abajo">
    <h3 class="panel-titulo">Módulos y Funcionalidades</h3>

    <?php if ($saasLocked): ?>
    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;margin-bottom:16px;background:#fef3c7;border:1px solid #fbbf24;border-radius:10px;font-size:.87rem;color:#92400e;">
        <i class="fas fa-lock" style="font-size:1rem;"></i>
        <span><strong>Control bloqueado por la plataforma SaaS.</strong> Estos módulos están siendo gestionados centralmente y no se pueden modificar desde aquí.
        <a href="../saas/estado.php" style="color:#b45309;font-weight:700;margin-left:6px;">Ver estado →</a></span>
    </div>
    <?php else: ?>
    <p class="texto-suave mb-4">Habilita o deshabilita módulos del sistema en tiempo real.</p>
    <?php endif; ?>

    <div class="feature-toggle-grid">
        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-user-plus feature-icon" style="color: #4f46e5;"></i>
                <div>
                    <div class="feature-label">Pre-matrícula</div>
                    <div class="feature-desc">Habilita el portal de admisión pública</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= $cfg['feature_prematricula'] ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_prematricula" <?= ($cfg['feature_prematricula'] ? 'checked' : '') ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-comments feature-icon" style="color: #10b981;"></i>
                <div>
                    <div class="feature-label">Sistema de Chat</div>
                    <div class="feature-desc">Mensajería instantánea entre usuarios</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= $cfg['feature_chat'] ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_chat" <?= ($cfg['feature_chat'] ? 'checked' : '') ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-boxes feature-icon" style="color: #f59e0b;"></i>
                <div>
                    <div class="feature-label">Inventario</div>
                    <div class="feature-desc">Gestión de recursos y préstamos</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= $cfg['feature_inventario'] ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_inventario" <?= ($cfg['feature_inventario'] ? 'checked' : '') ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-file-upload feature-icon" style="color: #8b5cf6;"></i>
                <div>
                    <div class="feature-label">Entrega de TFG</div>
                    <div class="feature-desc">Permite a los estudiantes subir su Trabajo Fin de Grado</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_subida_tfg'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_subida_tfg" <?= ($cfg['feature_subida_tfg'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-bullhorn feature-icon" style="color: #f43f5e;"></i>
                <div>
                    <div class="feature-label">Anuncios</div>
                    <div class="feature-desc">Tablón de avisos y comunicados del centro</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_anuncios'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_anuncios" <?= ($cfg['feature_anuncios'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-calendar-days feature-icon" style="color: #0ea5e9;"></i>
                <div>
                    <div class="feature-label">Eventos</div>
                    <div class="feature-desc">Calendario de eventos y actividades del centro</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_eventos'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_eventos" <?= ($cfg['feature_eventos'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-trophy feature-icon" style="color: #f59e0b;"></i>
                <div>
                    <div class="feature-label">Retos</div>
                    <div class="feature-desc">Desafíos y actividades académicas gamificadas</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_retos'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_retos" <?= ($cfg['feature_retos'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-envelope feature-icon" style="color: #6366f1;"></i>
                <div>
                    <div class="feature-label">Mensajería</div>
                    <div class="feature-desc">Sistema de reclamaciones y mensajes internos</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_mensajes'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_mensajes" <?= ($cfg['feature_mensajes'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-credit-card feature-icon" style="color: #10b981;"></i>
                <div>
                    <div class="feature-label">Pagos</div>
                    <div class="feature-desc">Gestión de pagos y matrículas de estudiantes</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_pagos'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_pagos" <?= ($cfg['feature_pagos'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-receipt feature-icon" style="color: #ef4444;"></i>
                <div>
                    <div class="feature-label">Gastos</div>
                    <div class="feature-desc">Control de gastos y categorías del centro</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_gastos'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_gastos" <?= ($cfg['feature_gastos'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-file-pdf feature-icon" style="color: #64748b;"></i>
                <div>
                    <div class="feature-label">Informes PDF</div>
                    <div class="feature-desc">Generación de boletines, listados y horarios en PDF</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_informes'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_informes" <?= ($cfg['feature_informes'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>

        <div class="feature-card<?= $saasLocked ? ' feature-card-locked' : '' ?>">
            <div class="feature-info">
                <i class="fas fa-calendar-alt feature-icon" style="color: #4f46e5;"></i>
                <div>
                    <div class="feature-label">Cuadro Horario</div>
                    <div class="feature-desc">Gestión de horarios de clases y asignaciones</div>
                </div>
            </div>
            <?php if ($saasLocked): ?>
                <span class="lock-badge"><i class="fas fa-lock"></i> <?= ($cfg['feature_horario'] ?? 1) ? 'Activo' : 'Inactivo' ?></span>
            <?php else: ?>
            <label class="switch">
                <input type="checkbox" class="toggle-feature" data-feature="feature_horario" <?= ($cfg['feature_horario'] ?? 1) ? 'checked' : '' ?>>
                <span class="slider round"></span>
            </label>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="panel margen-abajo">
    <h3 class="panel-titulo">Seguridad de la cuenta</h3>
    <div class="feature-card" style="max-width:560px">
        <div class="feature-info">
            <i class="fas fa-shield-alt feature-icon" style="color:<?= $mfaActivo ? '#10b981' : '#6b7280' ?>;"></i>
            <div>
                <div class="feature-label">Verificación en dos pasos (2FA)</div>
                <div class="feature-desc">
                    <?php if ($mfaActivo): ?>
                        Activa — tu cuenta está protegida con un autenticador TOTP.
                    <?php else: ?>
                        Inactiva — añade una segunda capa de seguridad a tu cuenta.
                    <?php endif; ?>
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

<form id="form-configuracion" method="POST" action="../../../controladores/admin/configuracion/guardar.php" enctype="multipart/form-data">
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
                <textarea name="textoLegal" rows="3" maxlength="1000"><?= Security::escapeHtml($cfg['textoLegal']) ?></textarea>
                <small class="texto-suave">Máx. 1000 caracteres</small>
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
                    <label class="cfg-logo-borrar">
                        <input type="checkbox" name="borrar_<?= $field ?>" value="1">
                        Eliminar logo actual
                    </label>
                <?php endif; ?>
                <input type="file" name="<?= $field ?>" accept="image/png,image/jpeg,image/webp">
                <small class="texto-suave">PNG, JPG o WEBP · máx. 2 MB · recomendado 200×80 px</small>
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
.cfg-logo-borrar { display:inline-flex; align-items:center; gap:6px; font-size:.8rem; color:#dc2626; cursor:pointer; margin-bottom:6px; }
.panel-titulo { font-size:.85rem; font-weight:700; letter-spacing:.05em; color:#6b7280; margin-bottom:16px; padding-bottom:8px; border-bottom:1px solid #e5e7eb; }
.feature-toggle-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px; }
.feature-card { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; transition: all 0.2s; }
.feature-card:hover { border-color: var(--accent); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.feature-info { display: flex; align-items: center; gap: 15px; }
.feature-icon { font-size: 1.25rem; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 10px; }
.feature-label { font-weight: 700; font-size: 0.95rem; color: #111827; }
.feature-desc { font-size: 0.8rem; color: #6b7280; }
.switch { position: relative; display: inline-block; width: 46px; height: 24px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: #4f46e5; }
input:checked + .slider:before { transform: translateX(22px); }
.feature-card-locked { opacity:.75; cursor:not-allowed; }
.feature-card-locked:hover { border-color:#e5e7eb; box-shadow:none; }
.lock-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px; border-radius:20px; font-size:.8rem; font-weight:700; background:#f3f4f6; color:#6b7280; white-space:nowrap; }
</style>

<?php include '../comunes/footer.php'; ?>

<script>
var saasLocked = <?= $saasLocked ? 'true' : 'false' ?>;
var csrfToken = '<?= Security::generateCSRFToken() ?>';
$(document).ready(function() {
    if (saasLocked) { $('.toggle-feature').prop('disabled', true); return; }
    $('.toggle-feature').on('change', function() {
        const feature = $(this).data('feature');
        const estado = $(this).is(':checked') ? 1 : 0;
        const $toggle = $(this);
        const $card = $(this).closest('.feature-card');

        $card.css('opacity', '0.6').css('pointer-events', 'none');

        $.ajax({
            url: '../../../controladores/admin/configuracion/toggle_feature.php',
            type: 'POST',
            dataType: 'json',
            data: {
                feature: feature,
                estado: estado,
                csrf_token: csrfToken
            },
            success: function(res) {
                $card.css('opacity', '1').css('pointer-events', 'all');
                if (res && res.new_csrf) {
                    csrfToken = res.new_csrf;
                    $('input[name="csrf_token"]').val(res.new_csrf);
                }
                if (!res || res.status !== 'success') {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                    var msg = (res && res.message) ? res.message : (res && res.msg ? res.msg : 'Error al actualizar el módulo. Recarga la página.');
                    if (window.Toast) Toast.show(msg, 'error');
                } else {
                    if (window.Toast) Toast.show(res.message || 'Configuración actualizada.', 'success');
                }
            },
            error: function(xhr) {
                $card.css('opacity', '1').css('pointer-events', 'all');
                $toggle.prop('checked', !$toggle.prop('checked'));
                var msg = xhr.status === 500
                    ? 'Error interno del servidor (500). Es posible que falten columnas en la base de datos. Contacta con soporte.'
                    : 'Error de conexión (' + xhr.status + '). Verifica tu conexión e inténtalo de nuevo.';
                if (window.Toast) Toast.show(msg, 'error');
            }
        });
    });
});
</script>

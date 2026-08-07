    </div><!-- /content -->
  </main>
</div><!-- /app -->

<div id="modal-borrar" class="modal-confirm-overlay" role="dialog" aria-modal="true" style="display:none;">
  <div class="modal-confirm-dialog">
    <div class="modal-confirm-header">
      <i class="fas fa-exclamation-triangle modal-confirm-icon" style="color: var(--rojo);"></i>
      <h3 id="modal-confirm-title" style="margin: 0; font-size: 18px; font-weight: 700; color: var(--text);">Eliminar <span id="modal-borrar-tipo"></span></h3>
    </div>
    <div class="modal-confirm-body">
      <p>Estás a punto de eliminar:</p>
      <div style="background: var(--surface-2); padding: 12px; border-radius: 8px; margin: 12px 0; color: var(--text); font-weight: 600;">
        <i class="fas fa-tag" style="margin-right: 8px; color: var(--dim);"></i>
        <span id="modal-borrar-nombre"></span>
        <span id="modal-borrar-extra" class="modal-extra-badge" style="display:none"></span>
      </div>
      <p style="color: var(--rojo); font-size: 0.9em; margin-bottom: 0;">
        <i class="fas fa-exclamation-circle"></i> <span id="modal-borrar-aviso">Esta acción es permanente y no se puede deshacer.</span>
      </p>

      <div id="modal-password-wrap" style="display:none; margin-top:16px;">
        <label for="modal-admin-password" style="display: block; font-size: 0.9em; font-weight: 600; color: var(--dim); margin-bottom: 6px;">
          <i class="fas fa-lock"></i> Confirma tu contraseña para continuar
        </label>
        <input type="password" id="modal-admin-password" style="width: 100%; padding: 10px 14px; border: 1px solid var(--border-2); border-radius: 8px; background: var(--surface); color: var(--text);"
               placeholder="Contraseña de administrador" autocomplete="current-password">
      </div>
    </div>
    <div class="modal-confirm-footer" style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
      <button type="button" id="modal-borrar-cancelar" style="padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; border: none; background: var(--surface-2); color: var(--dim);">Cancelar</button>
      <button type="button" id="modal-borrar-confirmar" style="padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; border: none; background: var(--rojo); color: #fff;">Sí, eliminar</button>
    </div>
    <input type="hidden" name="modal_csrf" value="<?= Security::generateCSRFToken() ?>">
  </div>
</div>

<script src="../../../public/js/core/utils.js?v=<?= filemtime(__DIR__.'/../../public/js/core/utils.js') ?>"></script>
<script>
$(document).ajaxError(function(event, xhr) {
    if (!window.Toast) return;
    if (xhr.status === 401) { Toast.show('Sesión expirada. Recarga la página.', 'error'); return; }
    if (xhr.status === 403) { Toast.show('Sin permisos para realizar esta acción.', 'error'); return; }
    if (xhr.status === 0)   { Toast.show('Sin conexión. Verifica tu red e inténtalo de nuevo.', 'error'); return; }
    if (xhr.status >= 500)  { Toast.show('Error del servidor (' + xhr.status + '). Contacta con soporte si persiste.', 'error'); return; }
});
</script>
<?php
// Bundle de los core/*.js que carga TODA página (dashboard-shell, onboarding-tour,
// filtros, paginacion, modal-borrar, modal-confirm, toast, upload-overlay, tooltip),
// generado por `npm run build:assets` (noDeploy/build-assets.js) — un solo <script>
// minificado en vez de varios sueltos. Si el bundle no existe (build no ejecutado
// en este entorno todavía) cae de vuelta a los ficheros sueltos sin minificar,
// para que un `git pull` sin `npm install` no rompa nada.
$__bundleJs = __DIR__ . '/../../public/js/core/bundle.min.js';
if (is_file($__bundleJs)):
?>
<script src="../../../public/js/core/bundle.min.js?v=<?= filemtime($__bundleJs) ?>"></script>
<?php else: ?>
<script src="../../../public/js/core/dashboard-shell.js?v=<?= filemtime(__DIR__.'/../../public/js/core/dashboard-shell.js') ?>"></script>
<script src="../../../public/js/core/onboarding-tour.js?v=<?= filemtime(__DIR__.'/../../public/js/core/onboarding-tour.js') ?>"></script>
<script src="../../../public/js/core/filtros.js"></script>
<script src="../../../public/js/core/paginacion.js"></script>
<script src="../../../public/js/core/modal-borrar.js"></script>
<script src="../../../public/js/core/modal-confirm.js"></script>
<script src="../../../public/js/core/toast.js"></script>
<script src="../../../public/js/core/upload-overlay.js"></script>
<script src="../../../public/js/core/tooltip.js"></script>
<?php endif; ?>
<?php
$__err = $errores ?? null;
$__ok  = $exito  ?? '';
// Keyed array = inline field errors already shown in form — no toast
$__err_str = is_string($__err) ? ($__err ?: null) : null;
if ($__err_str || $__ok):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($__err_str): ?>if (window.Toast) Toast.show(<?= json_encode($__err_str) ?>, 'error');<?php endif; ?>
    <?php if ($__ok): ?>if (window.Toast) Toast.show(<?= json_encode($__ok) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form').forEach(function(form) {
        if (!form.querySelector('input[type="file"]')) return;
        form.addEventListener('submit', function() {
            var btn = form.querySelector('[type="submit"]');
            if (!btn || btn.disabled) return;
            
            // Si el botón tiene 'name', creamos un input hidden para que no se pierda al hacer POST
            if (btn.name) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = btn.name;
                hidden.value = btn.value;
                form.appendChild(hidden);
            }
            
            btn.disabled = true;
            if (btn.tagName.toUpperCase() === 'BUTTON') {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
            } else {
                btn.value = 'Subiendo...';
            }
        });
    });

    // Toggle .has-file class on .zona-subida when a file is selected
    document.querySelectorAll('.zona-subida input[type="file"]').forEach(function(input) {
        var zone = input.closest('.zona-subida');
        if (!zone) return;
        var nameEl = zone.querySelector('span');
        var origText = nameEl ? nameEl.textContent : '';
        input.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                zone.classList.add('has-file');
                if (nameEl) {
                    nameEl.textContent = this.files.length === 1
                        ? this.files[0].name
                        : this.files.length + ' archivos seleccionados';
                }
            } else {
                zone.classList.remove('has-file');
                if (nameEl) nameEl.textContent = origText;
            }
        });
    });
});
</script>
<?php if (($seccion ?? '') !== 'inicio' && ($seccionActual ?? '') !== 'inicio'): ?>
<footer style="text-align:center;padding:14px 24px;border-top:1px solid var(--border);margin-top:32px;">
    <nav style="display:flex;justify-content:center;gap:18px;flex-wrap:wrap;">
        <a href="/vistas/legal/aviso-legal.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Aviso Legal</a>
        <a href="/vistas/legal/politica-de-privacidad.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Política de Privacidad</a>
        <a href="/vistas/legal/politica-de-cookies.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Política de Cookies</a>
        <a href="/vistas/legal/politica-de-gestion.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Política de Gestión</a>
    </nav>
</footer>
<?php endif; ?>
</body>
</html>

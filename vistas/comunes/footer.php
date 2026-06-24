    </div><!-- /content -->
  </main>
</div><!-- /app -->

<div id="modal-borrar" class="modal-backdrop" role="dialog" aria-modal="true">
  <div class="modal-caja">
    <div class="modal-icono"><i class="fas fa-trash-alt"></i></div>
    <h3 class="modal-titulo">Eliminar <span id="modal-borrar-tipo"></span></h3>
    <p class="modal-subtitulo">Estás a punto de eliminar:</p>
    <div class="modal-registro">
      <i class="fas fa-tag"></i>
      <span id="modal-borrar-nombre"></span>
      <span id="modal-borrar-extra" class="modal-extra-badge" style="display:none"></span>
    </div>
    <p class="modal-aviso"><i class="fas fa-exclamation-circle"></i> <span id="modal-borrar-aviso">Esta acción es permanente y no se puede deshacer.</span></p>
    <div id="modal-password-wrap" style="display:none;margin-top:16px;">
      <label for="modal-admin-password" class="modal-pw-label">
        <i class="fas fa-lock"></i> Confirma tu contraseña para continuar
      </label>
      <input type="password" id="modal-admin-password" class="modal-pw-input"
             placeholder="Contraseña de administrador" autocomplete="current-password">
    </div>
    <div class="modal-acciones">
      <button id="modal-borrar-cancelar" class="boton-secundario"><i class="fas fa-times"></i> Cancelar</button>
      <button id="modal-borrar-confirmar" class="boton-peligro"><i class="fas fa-trash"></i> Sí, eliminar</button>
    </div>
    <input type="hidden" name="modal_csrf" value="<?= Security::generateCSRFToken() ?>">
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
$(document).ajaxError(function(event, xhr) {
    if (!window.Toast) return;
    if (xhr.status === 401) { Toast.show('Sesión expirada. Recarga la página.', 'error'); return; }
    if (xhr.status === 403) { Toast.show('Sin permisos para realizar esta acción.', 'error'); return; }
    if (xhr.status === 0)   { Toast.show('Sin conexión. Verifica tu red e inténtalo de nuevo.', 'error'); return; }
    if (xhr.status >= 500)  { Toast.show('Error del servidor (' + xhr.status + '). Contacta con soporte si persiste.', 'error'); return; }
});
</script>
<script src="../../../public/js/dashboard-shell.js?v=<?= filemtime(__DIR__.'/../../public/js/dashboard-shell.js') ?>"></script>
<script src="../../../public/js/filtros.js"></script>
<script src="../../../public/js/paginacion.js"></script>
<script src="../../../public/js/modal-borrar.js"></script>
<script src="../../../public/js/toast.js"></script>
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
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
        });
    });
});
</script>
<footer style="text-align:center;padding:14px 24px;border-top:1px solid var(--border);margin-top:32px;">
    <nav style="display:flex;justify-content:center;gap:18px;flex-wrap:wrap;">
        <a href="/vistas/legal/aviso-legal.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Aviso Legal</a>
        <a href="/vistas/legal/politica-de-privacidad.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Política de Privacidad</a>
        <a href="/vistas/legal/politica-de-cookies.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Política de Cookies</a>
        <a href="/vistas/legal/politica-de-gestion.php" target="_blank" style="font-size:.78rem;color:var(--mut);text-decoration:none;" onmouseover="this.style.color='var(--accent)'" onmouseout="this.style.color='var(--mut)'">Política de Gestión</a>
    </nav>
</footer>
</body>
</html>

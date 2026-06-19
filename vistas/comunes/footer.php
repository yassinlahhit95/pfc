    </div><!-- /content -->
  </main>
</div><!-- /app -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="../../../public/js/dashboard-shell.js?v=<?= filemtime(__DIR__.'/../../public/js/dashboard-shell.js') ?>"></script>
<script src="../../../public/js/filtros.js"></script>
<script src="../../../public/js/paginacion.js"></script>
<script src="../../../public/js/toast.js"></script>
<?php
$__err = $errores ?? null;
$__ok  = $exito  ?? '';
$__err_str = is_array($__err) ? implode(' ', array_filter($__err)) : ($__err ?: null);
if ($__err_str || $__ok):
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($__err_str): ?>if (window.Toast) Toast.show(<?= json_encode($__err_str) ?>, 'error');<?php endif; ?>
    <?php if ($__ok): ?>if (window.Toast) Toast.show(<?= json_encode($__ok) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>
</body>
</html>

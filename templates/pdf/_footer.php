<?php
// Shared PDF footer — loaded in templates via: <?php include __DIR__ . '/_footer.php'; ?>
// Expected variables: $cfg, $cicloInfo (optional)
?>
<htmlpagefooter name="page-footer">
  <div class="pdf-footer">
    <?= htmlspecialchars($cfg['nombreCentro'] ?? 'AulaPro', ENT_QUOTES, 'UTF-8') ?>
    <?php if (!empty($cicloInfo)): ?>
      &nbsp;—&nbsp; <?= htmlspecialchars($cicloInfo, ENT_QUOTES, 'UTF-8') ?>
    <?php endif; ?>
    &nbsp;&nbsp;|&nbsp;&nbsp;
    Página {PAGENO} de {nb}
  </div>
</htmlpagefooter>

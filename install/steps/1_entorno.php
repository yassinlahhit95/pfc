<?php
// Paso 1 — comprobación de entorno (PHP, extensiones, permisos de escritura).
// Solo lectura: handlePost() no hace nada más que decidir si se puede avanzar.

function handlePost(): array {
    $checks = checkEnvironment();
    if (!environmentPasses($checks)) {
        return ['ok' => false, 'msg' => 'Resuelve los puntos marcados en rojo antes de continuar.'];
    }
    return ['ok' => true];
}

function renderStep(string $csrfToken): void {
    $checks = checkEnvironment();
    $puedeAvanzar = environmentPasses($checks);
    ?>
    <p class="install-intro">Comprobación automática de los requisitos del servidor antes de empezar.</p>
    <ul class="install-checklist">
      <?php foreach ($checks as $c): ?>
        <li class="<?= $c['ok'] ? 'ok' : ($c['bloqueante'] ? 'error' : 'aviso') ?>">
          <span class="install-check-icono"><?= $c['ok'] ? '✓' : ($c['bloqueante'] ? '✕' : '!') ?></span>
          <span>
            <strong><?= htmlspecialchars($c['label']) ?></strong>
            <small><?= htmlspecialchars($c['detail']) ?></small>
          </span>
        </li>
      <?php endforeach; ?>
    </ul>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
      <button type="submit" class="install-btn" <?= $puedeAvanzar ? '' : 'disabled' ?>>Continuar</button>
      <?php if (!$puedeAvanzar): ?>
        <p class="install-nota">Corrige los puntos marcados con ✕ y recarga esta página.</p>
      <?php endif; ?>
    </form>
    <?php
}

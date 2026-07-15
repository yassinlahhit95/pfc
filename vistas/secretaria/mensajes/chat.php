<?php
$seccion       = 'chat';
$titulo_pagina = 'Mensajería — Secretaría';
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_chat');
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../modelos/chat.php';

$myRol = 'secretaria';
$myId  = (int)$_SESSION['idSecretaria'];
$convs = chatConversacionesDe($myRol, $myId);
$csrf  = Security::generateCSRFToken();

function avaClassSec($rol) {
    return $rol === 'admin' ? 'ava-admin' : ($rol === 'profesor' ? 'ava-profesor' : 'ava-alumno');
}

require_once __DIR__ . '/../comunes/nav.php';
?>
<link rel="stylesheet" href="../../../public/css/features/chat.css">

<div class="chat-page" id="chat-page">

  <div class="chat-sidebar">
    <div class="chat-sidebar-head">
      <h2>Mensajes</h2>
      <button class="chat-new-btn" id="chat-new-btn" title="Nuevo chat">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
    </div>
    <div class="chat-search">
      <input type="text" placeholder="Buscar conversación..." id="sidebar-search" autocomplete="off">
    </div>
    <div class="chat-conv-list">
      <?php if (empty($convs)): ?>
        <div class="chat-empty-sidebar">No hay mensajes aún.<br>Contacte con un profesor o con el equipo directivo.</div>
      <?php else: foreach ($convs as $c):
        $initials = strtoupper(substr($c['other_nombre'], 0, 1) . (strpos($c['other_nombre'], ' ') !== false ? substr(strstr($c['other_nombre'], ' '), 1, 1) : ''));
        $preview  = mb_strimwidth($c['last_preview'] ?? '', 0, 40, '…');
        $time     = $c['last_message_at'] ? (new DateTime($c['last_message_at']))->format('H:i') : '';
      ?>
        <a href="conversacion.php?id=<?= (int)$c['id'] ?>" class="chat-conv-row">
          <div class="chat-ava <?= avaClassSec($c['other_rol']) ?>"><?= Security::escapeHtml($initials) ?></div>
          <div class="chat-conv-info">
            <div class="chat-conv-name"><?= Security::escapeHtml($c['other_nombre']) ?></div>
            <div class="chat-conv-preview"><?= Security::escapeHtml($preview) ?></div>
          </div>
          <div class="chat-conv-meta">
            <span class="chat-conv-time"><?= $time ?></span>
            <?php if ($c['unread_count'] > 0): ?>
              <span class="chat-unread-badge"><?= $c['unread_count'] ?></span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <div class="chat-main">
    <div class="chat-placeholder">
      <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <p>Seleccione una conversación o inicie una nueva</p>
    </div>
  </div>
</div>

<div class="chat-modal-overlay" id="chat-modal-overlay" style="display:none">
  <div class="chat-modal">
    <button class="chat-modal-close" id="chat-modal-close">&times;</button>
    <h3>Contactar</h3>
    <p class="small text-muted mb-3">Busque al director o a un profesor.</p>
    <input type="text" class="chat-modal-search" id="chat-modal-search" placeholder="Escriba el nombre…">
    <div class="chat-contact-list" id="chat-contact-list"></div>
  </div>
</div>

<script src="../../../public/js/features/chat.js"></script>
<script>
ChatModal.init({
    csrfToken: '<?= $csrf ?>',
    basePath:  '../../../'
});

document.getElementById('sidebar-search')?.addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('.chat-conv-row').forEach(row => {
        row.style.display = (row.querySelector('.chat-conv-name')?.textContent.toLowerCase() || '').includes(q) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

<?php
$seccion      = 'chat';
$titulo_pagina = 'Chat — Admin';
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_chat');
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../modelos/chat.php';

$myRol = 'admin';
$myId  = (int)$_SESSION['idAdmin'];
$convs = chatConversacionesDe($myRol, $myId);
$csrf  = Security::generateCSRFToken();

function avaClass($rol) {
    return $rol === 'admin' ? 'ava-admin' : ($rol === 'profesor' ? 'ava-profesor' : 'ava-alumno');
}

require_once __DIR__ . '/../comunes/nav.php';
?>
<link rel="stylesheet" href="../../../public/css/features/chat.css">

<div class="chat-page" id="chat-page">

  <!-- Sidebar -->
  <div class="chat-sidebar">
    <div class="chat-sidebar-head">
      <h2>Mensajes</h2>
      <button class="chat-new-btn" id="chat-new-btn" title="Nuevo chat">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
    </div>
    <div class="chat-search">
      <input type="text" placeholder="Buscar conversación..." id="sidebar-search"
        autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
        data-lpignore="true" data-1p-ignore="true" data-form-type="other">
    </div>
    <div class="chat-conv-list">
      <?php if (empty($convs)): ?>
        <div class="chat-empty-sidebar">No tienes conversaciones aún.<br>Pulsa + para iniciar una.</div>
      <?php else: foreach ($convs as $conversacion):
        $initials = strtoupper(substr($conversacion['other_nombre'], 0, 1) . (strpos($conversacion['other_nombre'], ' ') !== false ? substr(strstr($conversacion['other_nombre'], ' '), 1, 1) : ''));
        $preview  = mb_strimwidth($conversacion['last_preview'] ?? '', 0, 40, '…');
        $time     = $conversacion['last_message_at'] ? (new DateTime($conversacion['last_message_at']))->format('H:i') : '';
      ?>
        <a href="conversacion.php?id=<?= $conversacion['id'] ?>" class="chat-conv-row">
          <div class="chat-ava <?= avaClass($conversacion['other_rol']) ?>"><?= Security::escapeHtml($initials) ?></div>
          <div class="chat-conv-info">
            <div class="chat-conv-name"><?= Security::escapeHtml($conversacion['other_nombre']) ?></div>
            <div class="chat-conv-preview"><?= Security::escapeHtml($preview) ?></div>
          </div>
          <div class="chat-conv-meta">
            <span class="chat-conv-time"><?= $time ?></span>
            <?php if ($conversacion['unread_count'] > 0): ?>
              <span class="chat-unread-badge"><?= $conversacion['unread_count'] ?></span>
            <?php endif; ?>
          </div>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- Main placeholder -->
  <div class="chat-main">
    <div class="chat-placeholder">
      <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <p>Selecciona una conversación o inicia una nueva</p>
    </div>
  </div>
</div>

<!-- New chat modal -->
<div class="chat-modal-overlay" id="chat-modal-overlay" style="display:none">
  <div class="chat-modal">
    <button class="chat-modal-close" id="chat-modal-close">&times;</button>
    <h3>Nueva conversación</h3>
    <input type="text" class="chat-modal-search" id="chat-modal-search" placeholder="Buscar profesor o estudiante…"
    autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
    data-lpignore="true" data-1p-ignore="true" data-form-type="other">
    <div class="chat-contact-list" id="chat-contact-list"></div>
  </div>
</div>

<script src="../../../public/js/features/chat.js"></script>
<script>
ChatModal.init({
    csrfToken: '<?= $csrf ?>',
    basePath:  '../../../'
});

// Sidebar search filter
document.getElementById('sidebar-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.chat-conv-row').forEach(row => {
        const name = row.querySelector('.chat-conv-name')?.textContent.toLowerCase() || '';
        row.style.display = name.includes(q) ? '' : 'none';
    });
});

document.addEventListener('firebaseMessageReceived', function() {
    window.location.reload();
});
</script>
<?php require_once __DIR__ . '/../comunes/footer.php'; ?>

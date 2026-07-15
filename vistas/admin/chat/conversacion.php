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
$convId = (int)($_GET['id'] ?? 0);

$conv = $convId ? chatConversacionPorId($convId) : null;
if (!$conv || !chatEsParticipante($conv, $myRol, $myId)) {
    header('Location: index.php'); exit;
}

chatMarcarLeidos($convId, $myRol, $myId);

$otherRol    = ($conv['user_a_rol'] === $myRol && (int)$conv['user_a_id'] === $myId) ? $conv['user_b_rol'] : $conv['user_a_rol'];
$otherId     = ($conv['user_a_rol'] === $myRol && (int)$conv['user_a_id'] === $myId) ? (int)$conv['user_b_id'] : (int)$conv['user_a_id'];
$otherNombre = chatNombreUsuario($otherRol, $otherId);
$myNombre    = chatNombreUsuario($myRol, $myId);
$convs       = chatConversacionesDe($myRol, $myId);
$csrf        = Security::generateCSRFToken();

function avaClass($rol) {
    return $rol === 'admin' ? 'ava-admin' : ($rol === 'profesor' ? 'ava-profesor' : 'ava-alumno');
}

require_once __DIR__ . '/../comunes/nav.php';
?>
<link rel="stylesheet" href="../../../public/css/features/chat.css">

<div class="chat-page conv-open" id="chat-page">

  <!-- Sidebar -->
  <div class="chat-sidebar">
    <div class="chat-sidebar-head">
      <h2>Mensajes</h2>
      <button class="chat-new-btn" id="chat-new-btn" title="Nuevo chat">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      </button>
    </div>
    <div class="chat-search"><input type="text" placeholder="Buscar…" id="sidebar-search" autocomplete="off"></div>
    <div class="chat-conv-list">
      <?php foreach ($convs as $c):
        $initials = strtoupper(substr($c['other_nombre'], 0, 1) . (strpos($c['other_nombre'], ' ') !== false ? substr(strstr($c['other_nombre'], ' '), 1, 1) : ''));
        $preview  = mb_strimwidth($c['last_preview'] ?? '', 0, 40, '…');
        $time     = $c['last_message_at'] ? (new DateTime($c['last_message_at']))->format('H:i') : '';
      ?>
        <a href="conversacion.php?id=<?= $c['id'] ?>" class="chat-conv-row<?= $c['id'] == $convId ? ' active' : '' ?>">
          <div class="chat-ava <?= avaClass($c['other_rol']) ?>"><?= Security::escapeHtml($initials) ?></div>
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
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Conversation -->
  <div class="chat-main">
    <div class="chat-main-head">
      <button class="chat-back-btn" onclick="document.getElementById('chat-page').classList.remove('conv-open')">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="15 18 9 12 15 6"/></svg>
        Volver
      </button>
      <div class="chat-ava <?= avaClass($otherRol) ?>"><?= Security::escapeHtml(strtoupper(substr($otherNombre,0,1).(strpos($otherNombre,' ')!==false?substr(strstr($otherNombre,' '),1,1):''))) ?></div>
      <div>
        <div class="chat-main-name"><?= Security::escapeHtml($otherNombre) ?></div>
        <div class="chat-main-role"><?= ucfirst($otherRol) ?></div>
      </div>
    </div>
    <div class="chat-messages" id="chat-messages"></div>
    <div class="chat-compose">
      <textarea class="chat-compose-input" id="chat-input" placeholder="Escribe un mensaje… (Enter para enviar)" rows="1"></textarea>
      <button class="chat-send-btn" id="chat-send">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="chat-modal-overlay" id="chat-modal-overlay" style="display:none">
  <div class="chat-modal">
    <button class="chat-modal-close" id="chat-modal-close">&times;</button>
    <h3>Nueva conversación</h3>
    <input type="text" class="chat-modal-search" id="chat-modal-search" placeholder="Buscar…" autocomplete="off">
    <div class="chat-contact-list" id="chat-contact-list"></div>
  </div>
</div>

<script src="../../../public/js/features/chat.js"></script>
<script>
AulaChat.init({
    convId:    <?= $convId ?>,
    myRol:     '<?= $myRol ?>',
    myId:      <?= $myId ?>,
    csrfToken: '<?= $csrf ?>',
    basePath:  '../../../',
});
ChatModal.init({ csrfToken: '<?= $csrf ?>', basePath: '../../../' });

document.getElementById('sidebar-search')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.chat-conv-row').forEach(row => {
        const name = row.querySelector('.chat-conv-name')?.textContent.toLowerCase() || '';
        row.style.display = name.includes(q) ? '' : 'none';
    });
});
</script>
<?php require_once __DIR__ . '/../comunes/footer.php'; ?>

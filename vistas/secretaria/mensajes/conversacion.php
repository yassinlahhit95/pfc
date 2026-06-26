<?php
$seccion = 'chat';
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_chat');
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../modelos/chat.php';

$convId = (int)($_GET['id'] ?? 0);
$myRol  = 'secretaria';
$myId   = (int)$_SESSION['idSecretaria'];

$conv = chatConversacionPorId($convId);
if (!$conv || !chatEsParticipante($conv, $myRol, $myId)) {
    header('Location: chat.php');
    exit;
}

$titulo_pagina = 'Chat con ' . Security::escapeHtml($conv['other_nombre'] ?? '');

function avaClassConv($rol) {
    return $rol === 'admin' ? 'ava-admin' : ($rol === 'profesor' ? 'ava-profesor' : 'ava-alumno');
}

require_once __DIR__ . '/../comunes/nav.php';
?>
<link rel="stylesheet" href="../../../public/css/chat.css">

<div class="chat-page" id="chat-page">

  <div class="chat-sidebar chat-sidebar-hidden-mobile">
    <div class="chat-sidebar-head">
      <a href="chat.php" class="chat-back-btn">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      </a>
      <h2>Mensajes</h2>
    </div>
    <div class="chat-conv-list">
      <?php
      $convs = chatConversacionesDe($myRol, $myId);
      foreach ($convs as $c):
        $initials = strtoupper(substr($c['other_nombre'], 0, 1) . (strpos($c['other_nombre'], ' ') !== false ? substr(strstr($c['other_nombre'], ' '), 1, 1) : ''));
        $active   = ($c['id'] == $convId) ? ' active' : '';
      ?>
        <a href="conversacion.php?id=<?= (int)$c['id'] ?>" class="chat-conv-row<?= $active ?>">
          <div class="chat-ava <?= avaClassConv($c['other_rol']) ?>"><?= Security::escapeHtml($initials) ?></div>
          <div class="chat-conv-info">
            <div class="chat-conv-name"><?= Security::escapeHtml($c['other_nombre']) ?></div>
            <div class="chat-conv-preview"><?= Security::escapeHtml($c['last_preview'] ?? '') ?></div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="chat-main">
    <div class="chat-main-head">
      <div class="chat-main-user">
        <div class="chat-ava <?= avaClassConv($conv['other_rol'] ?? '') ?> small">
          <?= Security::escapeHtml(strtoupper(substr($conv['other_nombre'] ?? '?', 0, 1))) ?>
        </div>
        <div>
          <div class="chat-main-name"><?= Security::escapeHtml($conv['other_nombre'] ?? '') ?></div>
          <div class="chat-main-status">En línea ahora</div>
        </div>
      </div>
    </div>

    <div class="chat-messages" id="chat-messages">
      <div class="chat-loading"><i class="fas fa-circle-notch fa-spin"></i> Cargando mensajes...</div>
    </div>

    <div class="chat-input-area">
      <form id="chat-form" class="chat-form">
        <textarea id="chat-input" placeholder="Escribe un mensaje…" rows="1"></textarea>
        <button type="submit" id="chat-send" title="Enviar mensaje">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="22" y1="2" x2="11" y2="13"/><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </form>
    </div>
  </div>
</div>

<script src="../../../public/js/chat.js"></script>
<script>
AulaChat.init({
    convId:    <?= $convId ?>,
    myRol:     'secretaria',
    myId:      <?= $myId ?>,
    csrfToken: '<?= Security::generateCSRFToken() ?>',
    basePath:  '../../../'
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

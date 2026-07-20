<?php
// Ensure this partial receives required variables:
// $cw_rol          (e.g., 'admin', 'profesor', 'estudiante', 'secretaria', 'tutor')
// $cw_id           (the user's ID)
// $cw_unreadCount  (total unread messages for the user)
// $cw_basePath     (the base path to public directory, e.g., '../../../')

if (!isset($cw_rol, $cw_id, $cw_unreadCount, $cw_basePath)) {
    return; // Silent abort if not properly included
}
?>
<div id="cw" class="cw-wrap">
  <div class="cw-overlay" id="cw-overlay" hidden></div>
  <div class="cw-window" id="cw-window" hidden>
    <div class="cw-head">
      <h2 class="cw-head-title">Mensajes</h2>
      <div class="cw-head-actions">
        <button class="cw-btn-icon" id="cw-new" title="Nueva conversación">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </button>
        <button class="cw-btn-icon" id="cw-close" title="Cerrar">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
    </div>
    <!-- List panel -->
    <div class="cw-panel" id="cw-list-panel">
      <div class="cw-conv-list" id="cw-list"><div class="cw-loading">Cargando…</div></div>
    </div>
    <!-- Conversation panel -->
    <div class="cw-panel" id="cw-conv-panel" hidden>
      <div class="cw-conv-head">
        <button class="cw-btn-icon" id="cw-back" title="Volver">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="cw-ava" id="cw-conv-ava"></div>
        <div class="cw-conv-head-info">
          <div class="cw-conv-head-name" id="cw-conv-name"></div>
          <div class="cw-conv-head-role" id="cw-conv-role"></div>
        </div>
      </div>
      <div class="cw-messages" id="cw-messages"></div>
      <div class="cw-compose">
        <textarea class="cw-input" id="cw-input" placeholder="Escribe un mensaje… (Enter para enviar)" rows="1"></textarea>
        <button class="cw-send" id="cw-send" title="Enviar">
          <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
    </div>
    <!-- Contacts panel -->
    <div class="cw-panel" id="cw-contacts-panel" hidden>
      <div class="cw-conv-head">
        <button class="cw-btn-icon" id="cw-contacts-back" title="Volver">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <span style="font-weight:700;font-size:.9rem;flex:1">Nueva conversación</span>
      </div>
      <div class="cw-search-wrap">
        <input class="cw-search-input" id="cw-contact-search" type="search" placeholder="Buscar persona…"
               autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
               data-lpignore="true" data-1p-ignore="true" data-form-type="other">
      </div>
      <div class="cw-contact-list" id="cw-contacts"></div>
    </div>
  </div>
  <button class="cw-fab" id="cw-fab" aria-label="Abrir mensajería">
    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    <span class="cw-fab-badge" id="cw-fab-badge"<?= $cw_unreadCount > 0 ? '' : ' hidden' ?>><?= (int)$cw_unreadCount ?></span>
  </button>
</div>

<script src="<?= $cw_basePath ?>public/js/features/chat-widget.js?v=<?= @filemtime(__DIR__.'/../../public/js/features/chat-widget.js') ?>"></script>
<script>
ChatWidget.init({
  myRol:       '<?= Security::escapeHtml($cw_rol) ?>',
  myId:        <?= (int)$cw_id ?>,
  csrfToken:   '<?= Security::generateCSRFToken() ?>',
  basePath:    '<?= $cw_basePath ?>',
  unreadCount: <?= (int)$cw_unreadCount ?>,
});
</script>

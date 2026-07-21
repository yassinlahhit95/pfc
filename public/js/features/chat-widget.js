/**
 * AulaPro — Floating Chat Widget
 * Self-contained IIFE. Exposes window.ChatWidget.init(cfg).
 */
(function () {
  'use strict';

  /* â”€â”€ State â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  let BASE       = '/';
  let myRol      = null;
  let myId       = 0;
  let csrfToken  = '';

  let isOpen        = false;
  let currentView   = 'list';   // 'list' | 'conv' | 'contacts'
  let currentConvId = 0;
  let lastMsgId     = 0;
  let lastDateLabel = '';
  let pollTimer     = null;
  let pollInterval  = 3000;
  let contactTimer  = null;

  /* â”€â”€ Helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  const $ = id => document.getElementById(id);

  function escHtml(str) {
    return String(str || '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function avaClass(rol) {
    const map = {
      admin:      'cw-ava cw-ava-admin',
      profesor:   'cw-ava cw-ava-profesor',
      estudiante: 'cw-ava cw-ava-alumno',
      secretaria: 'cw-ava cw-ava-secretaria',
      tutor:      'cw-ava cw-ava-tutor',
    };
    return map[rol] || 'cw-ava cw-ava-admin';
  }

  function avaInit(nombre) {
    const parts = (nombre || '?').trim().split(/\s+/);
    return ((parts[0]?.[0] || '') + (parts[1]?.[0] || '')).toUpperCase() || '?';
  }

  function fmtTime(str) {
    if (!str) return '';
    const sp = str.indexOf(' ');
    return sp >= 0 ? str.substring(sp + 1, sp + 6) : '';
  }

  function fmtDate(str) {
    if (!str) return '';
    const dateOnly = str.substring(0, 10);
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const today = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
    if (dateOnly === today) return 'Hoy';
    const yest = new Date(now);
    yest.setDate(yest.getDate() - 1);
    const yesterStr = `${yest.getFullYear()}-${pad(yest.getMonth() + 1)}-${pad(yest.getDate())}`;
    if (dateOnly === yesterStr) return 'Ayer';
    return dateOnly.split('-').reverse().join('/');
  }

  function roleLabel(rol) {
    const map = { admin: 'Admin', profesor: 'Profesor', estudiante: 'Alumno', tutor: 'Tutor', secretaria: 'Secretaria' };
    return map[rol] || rol;
  }

  function resolveUrl(path) {
    if (path.startsWith('/') || path.startsWith('http')) return path;
    const parts = location.pathname.split('/');
    const vi = parts.indexOf('vistas');
    const base = vi > -1 ? parts.slice(0, vi).join('/') : '';
    return base + '/' + path.replace(/^(\.\.\/)+/, '');
  }

  /* â”€â”€ Badge â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function updateBadge(count) {
    const badge = $('cw-fab-badge');
    if (!badge) return;
    badge.textContent = count;
    badge.hidden = count <= 0;
  }

  /* â”€â”€ Window open/close â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function toggleWindow() {
    isOpen ? closeWindow() : openWindow();
  }

  function openWindow() {
    isOpen = true;
    const win = $('cw-window');
    if (win) win.hidden = false;
    const overlay = $('cw-overlay');
    if (overlay) overlay.hidden = false;
    $('cw-fab')?.classList.add('open');
    if (currentView === 'list') loadConversations();
    // El sondeo en segundo plano puede estar a 30s: al abrir volvemos al ritmo rÃ¡pido
    pollInterval = 3000;
    schedulePoll();
  }

  function closeWindow() {
    isOpen = false;
    const win = $('cw-window');
    if (win) win.hidden = true;
    const overlay = $('cw-overlay');
    if (overlay) overlay.hidden = true;
    $('cw-fab')?.classList.remove('open');
    // We intentionally don't stop poll here, we just change interval/endpoint in fetchNew
  }

  /* â”€â”€ Panels â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function showPanel(name) {
    currentView = name;
    ['list', 'conv', 'contacts'].forEach(v => {
      const el = $('cw-' + v + '-panel');
      if (el) el.hidden = (v !== name);
    });
  }

  /* â”€â”€ Conversation list â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function loadConversations() {
    const list = $('cw-list');
    if (list) list.innerHTML = '<div class="cw-loading"><span class="cw-spinner"></span>Cargando…</div>';
    fetch(resolveUrl(BASE + 'controladores/chat/conversaciones.php'), { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        if (data.ok) renderConversations(data.convs);
        else if (list) list.innerHTML = '<div class="cw-empty">Error al cargar conversaciones</div>';
      })
      .catch(() => {
        if (list) list.innerHTML = '<div class="cw-empty">Sin conexiÃ³n</div>';
      });
  }

  function renderConversations(convs) {
    const list = $('cw-list');
    if (!list) return;
    if (!convs || !convs.length) {
      list.innerHTML = '<div class="cw-empty">Sin conversaciones. Crea una nueva con el botÃ³n +</div>';
      return;
    }
    list.innerHTML = '';
    convs.forEach(conv => {
      const row = document.createElement('div');
      row.className = 'cw-conv-row';

      const ava = document.createElement('div');
      ava.className = avaClass(conv.other_rol);
      ava.textContent = avaInit(conv.other_nombre);

      const info = document.createElement('div');
      info.className = 'cw-conv-info';
      info.innerHTML = `<div class="cw-conv-name">${escHtml(conv.other_nombre)}</div>
                        <div class="cw-conv-preview">${escHtml(conv.last_preview)}</div>`;

      const meta = document.createElement('div');
      meta.className = 'cw-conv-meta';
      meta.innerHTML = `<div class="cw-conv-time">${escHtml(fmtTime(conv.last_at || ''))}</div>`;
      if (conv.unread > 0) {
        meta.innerHTML += `<div class="cw-conv-badge">${conv.unread}</div>`;
      }

      row.appendChild(ava);
      row.appendChild(info);
      row.appendChild(meta);
      row.addEventListener('click', () => openConversation(conv.id, conv.other_nombre, conv.other_rol));
      list.appendChild(row);
    });
  }

  /* â”€â”€ Open a conversation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function openConversation(convId, name, rol) {
    stopPoll();
    currentConvId = convId;
    lastMsgId     = 0;
    lastDateLabel = '';

    // Update header
    const avaEl = $('cw-conv-ava');
    if (avaEl) { avaEl.className = avaClass(rol); avaEl.textContent = avaInit(name); }
    const nameEl = $('cw-conv-name');
    if (nameEl) nameEl.textContent = name;
    const roleEl = $('cw-conv-role');
    if (roleEl) roleEl.textContent = roleLabel(rol);

    // Clear + show loading
    const msgBox = $('cw-messages');
    if (msgBox) msgBox.innerHTML = '<div class="cw-loading"><span class="cw-spinner"></span>Cargando…</div>';

    showPanel('conv');

    fetch(resolveUrl(BASE + 'controladores/chat/mensajes.php') + '?conv_id=' + convId, { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => {
        if (data.ok) renderMessages(data.messages, true);
      })
      .catch(() => {});

    startPoll();

    // Focus input
    setTimeout(() => $('cw-input')?.focus(), 100);
  }

  /* â”€â”€ Messages â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function renderMessages(messages, initial) {
    const box = $('cw-messages');
    if (!box) return;
    if (initial) { box.innerHTML = ''; lastDateLabel = ''; }
    messages.forEach(msg => appendMsg(msg));
    if (messages.length) {
      lastMsgId = Math.max(lastMsgId, parseInt(messages[messages.length - 1].id));
      scrollBottom(box);
    }
  }

  function appendMsg(msg) {
    const box = $('cw-messages');
    if (!box) return;
    const isMe = (msg.emisor_rol === myRol && parseInt(msg.emisor_id) === myId);

    // Date separator
    const dateLabel = fmtDate(msg.fecha);
    if (dateLabel && dateLabel !== lastDateLabel) {
      lastDateLabel = dateLabel;
      const sep = document.createElement('div');
      sep.className = 'cw-date-sep';
      sep.textContent = dateLabel;
      box.appendChild(sep);
    }

    const wrap = document.createElement('div');
    wrap.className = 'cw-msg-wrap ' + (isMe ? 'out' : 'in');

    const bubble = document.createElement('div');
    bubble.className = 'cw-bubble';
    bubble.textContent = msg.contenido;

    const time = document.createElement('div');
    time.className = 'cw-msg-time';
    time.textContent = fmtTime(msg.fecha);

    wrap.appendChild(bubble);
    wrap.appendChild(time);
    box.appendChild(wrap);

    const msgId = parseInt(msg.id || 0);
    if (!isMe && lastMsgId > 0 && msgId > lastMsgId) {
        if (navigator.vibrate) navigator.vibrate([200, 100, 200]);
    }

    lastMsgId = Math.max(lastMsgId, msgId);
    scrollBottom(box);
  }

  function scrollBottom(box) {
    if (box) box.scrollTop = box.scrollHeight;
  }

  /* â”€â”€ Polling â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function startPoll() {
    pollInterval = 3000;
    schedulePoll();
  }

  function stopPoll() {
    clearTimeout(pollTimer);
    pollTimer = null;
  }

  function schedulePoll() {
    clearTimeout(pollTimer);
    let delay = pollInterval;
    if (document.hidden) {
      delay = Math.max(pollInterval, 15000);
    } else if (isOpen && currentView === 'conv' && currentConvId > 0) {
      delay = Math.min(pollInterval, 6000);  // conversaciÃ³n abierta y visible: casi tiempo real
    } else if (isOpen) {
      delay = Math.min(pollInterval, 10000); // lista abierta: refresco frecuente
    }
    pollTimer = setTimeout(fetchNew, delay);
  }

  function fetchNew() {
    if (isOpen && currentConvId > 0 && currentView === 'conv') {
      const url = resolveUrl(BASE + 'controladores/chat/mensajes.php')
                + '?conv_id=' + currentConvId + '&after_id=' + lastMsgId;
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => {
          if (data.ok && data.messages.length) {
            data.messages.forEach(msg => appendMsg(msg));
            pollInterval = 3000;
          } else {
            pollInterval = Math.min(Math.round(pollInterval * 1.4), 30000);
          }
          schedulePoll();
        })
        .catch(() => {
          pollInterval = Math.min(pollInterval * 2, 30000);
          schedulePoll();
        });
    } else {
      // Poll conversation list to update badges and list view dynamically
      const url = resolveUrl(BASE + 'controladores/chat/conversaciones.php');
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => {
          if (data.ok) {
            if (isOpen && currentView === 'list') {
              // Instead of showing a loading state, silently render list
              renderConversations(data.convs);
            }
            let unread = 0;
            data.convs.forEach(conv => { unread += (parseInt(conv.unread) || 0); });
            
            const currentBadgeStr = $('cw-fab-badge')?.textContent || '0';
            const currentBadge = parseInt(currentBadgeStr, 10) || 0;
            if (currentBadge < unread && navigator.vibrate) {
               navigator.vibrate([200, 100, 200]);
            }
            updateBadge(unread);
            
            // Also update the sidebar badge if it exists
            const navBadge = document.querySelector('.nav-item[href*="chat"] .nav-badge-alert');
            if (navBadge) {
               if (unread > 0) { navBadge.textContent = unread; navBadge.style.display = ''; }
               else { navBadge.style.display = 'none'; }
            } else if (unread > 0) {
               const navItem = document.querySelector('.nav-item[href*="chat"]');
               if (navItem) {
                  const badgeEl = document.createElement('span');
                  badgeEl.className = 'nav-badge nav-badge-alert';
                  badgeEl.textContent = unread;
                  navItem.insertBefore(badgeEl, navItem.querySelector('.nav-rail'));
               }
            }
            pollInterval = Math.min(Math.round(pollInterval * 1.4), 30000);
          }
          schedulePoll();
        })
        .catch(() => {
          pollInterval = Math.min(pollInterval * 2, 30000);
          schedulePoll();
        });
    }
  }

  /* â”€â”€ Send â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function sendMessage() {
    const input = $('cw-input');
    const text = (input?.value || '').trim();
    if (!text || !currentConvId) return;
    input.value = '';
    input.style.height = '';

    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    fd.append('conv_id',    currentConvId);
    fd.append('contenido',  text);

    fetch(resolveUrl(BASE + 'controladores/chat/enviar.php'), {
      method: 'POST', body: fd, credentials: 'same-origin'
    })
      .then(r => r.json())
      .then(data => { if (data.ok && data.message) appendMsg(data.message); })
      .catch(() => {});
  }

  /* â”€â”€ Contacts â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function showContacts() {
    showPanel('contacts');
    const search = $('cw-contact-search');
    if (search) { search.value = ''; search.focus(); }
    loadContacts('');
  }

  function loadContacts(q) {
    const list = $('cw-contacts');
    if (list) list.innerHTML = '<div class="cw-loading"><span class="cw-spinner"></span>Buscando…</div>';
    fetch(resolveUrl(BASE + 'controladores/chat/contactos.php') + '?q=' + encodeURIComponent(q), { credentials: 'same-origin' })
      .then(r => r.json())
      .then(data => { if (data.ok) renderContacts(data.contacts); })
      .catch(() => {});
  }

  function renderContacts(contacts) {
    const list = $('cw-contacts');
    if (!list) return;
    if (!contacts || !contacts.length) {
      list.innerHTML = '<div class="cw-empty">Sin resultados</div>';
      return;
    }
    list.innerHTML = '';
    contacts.forEach(contact => {
      const row = document.createElement('div');
      row.className = 'cw-contact-row';

      const ava = document.createElement('div');
      ava.className = avaClass(contact.rol);
      ava.textContent = avaInit(contact.nombre);

      const info = document.createElement('div');
      info.innerHTML = `<div class="cw-contact-name">${escHtml(contact.nombre)}</div>
                        <div class="cw-contact-role">${roleLabel(contact.rol)}</div>`;

      row.appendChild(ava);
      row.appendChild(info);
      row.addEventListener('click', () => startConversation(contact));
      list.appendChild(row);
    });
  }

  function startConversation(contact) {
    const fd = new FormData();
    fd.append('csrf_token', csrfToken);
    fd.append('target_rol', contact.rol);
    fd.append('target_id',  contact.uid);

    const list = $('cw-contacts');
    if (list) list.innerHTML = '<div class="cw-loading"><span class="cw-spinner"></span>Abriendo…</div>';

    fetch(resolveUrl(BASE + 'controladores/chat/iniciar.php'), {
      method: 'POST', body: fd, credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
    })
      .then(r => r.json())
      .then(data => {
        if (data.ok && data.conv_id) {
          openConversation(data.conv_id, contact.nombre, contact.rol);
        }
      })
      .catch(() => {
        if (list) list.innerHTML = '<div class="cw-empty">Error al iniciar chat</div>';
      });
  }

  /* â”€â”€ Keyboard Adjustments for Mobile (iOS & Android) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function adjustForKeyboard() {
    const win = $('cw-window');
    if (!win || win.hidden) return;

    if (window.innerWidth > 600) {
      win.style.bottom = '';
      win.style.top = '';
      win.style.height = '';
      return;
    }

    if (window.visualViewport) {
      const viewport = window.visualViewport;
      const keyboardHeight = window.innerHeight - viewport.height;

      if (keyboardHeight > 100) {
        win.style.bottom = (keyboardHeight + 8) + 'px';
        win.style.height = (viewport.height - 80) + 'px';
        win.style.top = 'auto';
      } else {
        win.style.bottom = '';
        win.style.top = '';
        win.style.height = '';
      }
    }
  }

  /* â”€â”€ Boot â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function boot() {
    const fab = $('cw-fab');
    if (!fab) return;

    fab.addEventListener('click', toggleWindow);
    $('cw-close')?.addEventListener('click', closeWindow);
    $('cw-new')?.addEventListener('click', showContacts);

    $('cw-back')?.addEventListener('click', () => {
      currentConvId = 0;
      showPanel('list');
      loadConversations();
    });
    $('cw-contacts-back')?.addEventListener('click', () => showPanel('list'));

    $('cw-send')?.addEventListener('click', sendMessage);

    const input = $('cw-input');
    if (input) {
      input.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
      });
      input.addEventListener('input', () => {
        input.style.height = '';
        input.style.height = Math.min(input.scrollHeight, 100) + 'px';
      });
      input.addEventListener('focus', () => {
        setTimeout(adjustForKeyboard, 300);
      });
      input.addEventListener('blur', () => {
        setTimeout(adjustForKeyboard, 300);
      });
    }

    const search = $('cw-contact-search');
    if (search) {
      search.addEventListener('input', e => {
        clearTimeout(contactTimer);
        contactTimer = setTimeout(() => loadContacts(e.target.value), 300);
      });
    }

    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) {
        clearTimeout(pollTimer);
        pollInterval = 3000;
        fetchNew();
      }
    });

    if (window.visualViewport) {
      window.visualViewport.addEventListener('resize', adjustForKeyboard);
      window.visualViewport.addEventListener('scroll', adjustForKeyboard);
    }

    // Close on outside click (desktop) or overlay tap (mobile)
    document.addEventListener('click', e => {
      if (isOpen && !e.target.closest('#cw')) closeWindow();
    });
    $('cw-overlay')?.addEventListener('click', closeWindow);
  }

  if (document.readyState !== 'loading') {
    boot();
  } else {
    document.addEventListener('DOMContentLoaded', boot);
  }

  /* â”€â”€ Public API â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  window.ChatWidget = {
    init(cfg) {
      myRol      = cfg.myRol;
      myId       = cfg.myId;
      csrfToken  = cfg.csrfToken;
      if (cfg.basePath) BASE = cfg.basePath;
      updateBadge(parseInt(cfg.unreadCount || 0, 10));
      startPoll(); // Start background polling immediately
    },
    updateBadge,

    startWith(targetRol, targetId, nombre) {
      if (!isOpen) openWindow();
      const msgBox = $('cw-messages');
      if (msgBox) msgBox.innerHTML = '<div class="cw-loading"><span class="cw-spinner"></span>Iniciando…</div>';
      showPanel('conv');

      const avaEl = $('cw-conv-ava');
      if (avaEl) { avaEl.className = avaClass(targetRol); avaEl.textContent = avaInit(nombre); }
      const nameEl = $('cw-conv-name');
      if (nameEl) nameEl.textContent = nombre;
      const roleEl = $('cw-conv-role');
      if (roleEl) roleEl.textContent = roleLabel(targetRol);

      const fd = new FormData();
      fd.append('csrf_token', csrfToken);
      fd.append('target_rol', targetRol);
      fd.append('target_id',  targetId);

      fetch(resolveUrl(BASE + 'controladores/chat/iniciar.php'), {
        method: 'POST', body: fd, credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then(r => r.json())
        .then(data => {
          if (data.ok && data.conv_id) openConversation(data.conv_id, nombre, targetRol);
          else if (msgBox) msgBox.innerHTML = '<div class="cw-empty">No se pudo iniciar el chat</div>';
        })
        .catch(() => {
          if (msgBox) msgBox.innerHTML = '<div class="cw-empty">Error de conexiÃ³n</div>';
        });
    },
  };
})();

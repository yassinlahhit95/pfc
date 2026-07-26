/**
 * AulaChat — AJAX messaging + polling
 * Usage: AulaChat.init({convId, myRol, myId, csrfToken, basePath})
 *        ChatModal.init({csrfToken, basePath})
 */

// ── Helpers ───────────────────────────────────────────────────────────────────
function avaClass(rol) {
    return rol === 'admin' ? 'ava-admin' : rol === 'profesor' ? 'ava-profesor' : 'ava-alumno';
}
function avaInit(nombre) {
    const parts = (nombre || '?').trim().split(/\s+/);
    return ((parts[0]?.[0] || '') + (parts[1]?.[0] || '')).toUpperCase();
}
// Extract date string directly (avoids timezone conversion of server timestamps)
function fmtDate(fechaStr) {
    if (!fechaStr) return '';
    const dateOnly = fechaStr.substring(0, 10); // 'YYYY-MM-DD'
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const todayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;
    if (dateOnly === todayStr) return 'Hoy';
    const yest = new Date(now);
    yest.setDate(yest.getDate() - 1);
    const yesterStr = `${yest.getFullYear()}-${pad(yest.getMonth() + 1)}-${pad(yest.getDate())}`;
    if (dateOnly === yesterStr) return 'Ayer';
    const [y, m, d] = dateOnly.split('-');
    return `${d}/${m}/${y}`;
}
function fmtDateTime(fechaStr) {
    if (!fechaStr) return '';
    const sp = fechaStr.indexOf(' ');
    if (sp < 0) return fechaStr;
    const [y, m, d] = fechaStr.substring(0, sp).split('-');
    const time = fechaStr.substring(sp + 1, sp + 6); // 'HH:MM'
    return `${d}/${m}/${y} - ${time}`;
}

// ── Message sound (Web Audio, no asset needed) ─────────────────────────────────
let _audioCtx = null;
function playSound(dir) {
    try {
        const Ctx = window.AudioContext || window.webkitAudioContext;
        if (!Ctx) return;
        _audioCtx = _audioCtx || new Ctx();
        if (_audioCtx.state === 'suspended') _audioCtx.resume();
        const ctx = _audioCtx;
        const now = ctx.currentTime;
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        if (dir === 'in') {
            // Incoming: rising two-tone chime
            osc.frequency.setValueAtTime(660, now);
            osc.frequency.setValueAtTime(880, now + 0.10);
        } else {
            // Outgoing: single soft tick
            osc.frequency.setValueAtTime(520, now);
        }
        gain.gain.setValueAtTime(0.0001, now);
        gain.gain.exponentialRampToValueAtTime(0.7, now + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + (dir === 'in' ? 0.3 : 0.16));
        osc.start(now);
        osc.stop(now + (dir === 'in' ? 0.32 : 0.18));
    } catch (e) { /* audio unavailable */ }
}

// ── AulaChat ──────────────────────────────────────────────────────────────────
window.AulaChat = (function () {
    let cfg = {};
    let lastMsgId = 0;
    let lastDateLabel = '';
    let lastSeenId = 0;
    let hasPendingSeen = false;
    let pollTimer = null;
    let pollInterval = 3000;       // current adaptive interval (ms)
    const POLL_MIN_MS  = 3000;    // fastest: active tab with recent messages
    const POLL_MAX_MS  = 30000;   // slowest: idle or background
    const POLL_HIDDEN_MS = 15000; // background tab base interval

    const el = {
        messages: () => document.getElementById('chat-messages'),
        input:    () => document.getElementById('chat-input'),
        sendBtn:  () => document.getElementById('chat-send'),
    };

    function scrollBottom(force) {
        const box = el.messages();
        if (!box) return;
        if (force || box.scrollTop + box.clientHeight >= box.scrollHeight - 80) {
            box.scrollTop = box.scrollHeight;
        }
    }

    function buildBubble(msg, isMe) {
        const wrap = document.createElement('div');
        wrap.className = 'chat-msg-wrap ' + (isMe ? 'out' : 'in');
        wrap.dataset.id = msg.id;

        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.textContent = msg.contenido;

        const meta = document.createElement('div');
        meta.className = 'chat-msg-meta';

        const time = document.createElement('span');
        time.className = 'chat-msg-time';
        time.textContent = fmtDateTime(msg.fecha);
        meta.appendChild(time);

        if (isMe) {
            const tick = document.createElement('span');
            const seen = parseInt(msg.leido) === 1;
            tick.className = 'chat-tick' + (seen ? ' seen' : '');
            tick.dataset.msgId = msg.id;
            meta.appendChild(tick);
            if (seen) lastSeenId = Math.max(lastSeenId, parseInt(msg.id));
            else hasPendingSeen = true;
        }

        wrap.appendChild(bubble);
        wrap.appendChild(meta);
        return wrap;
    }

    function maybeDateSep(fechaStr) {
        const label = fmtDate(fechaStr);
        if (label === lastDateLabel) return null;
        lastDateLabel = label;
        const sep = document.createElement('div');
        sep.className = 'chat-date-sep';
        sep.textContent = label;
        return sep;
    }

    function renderAll(messages) {
        const box = el.messages();
        if (!box) return;
        box.innerHTML = '';
        lastDateLabel = '';
        lastSeenId = 0;
        hasPendingSeen = false;
        messages.forEach(msg => appendMsg(msg, false));
        scrollBottom(true);
        if (messages.length) lastMsgId = Math.max(lastMsgId, parseInt(messages[messages.length - 1].id));
    }

    function appendMsg(msg, scroll = true) {
        const box = el.messages();
        if (!box) return;
        const isMe = (msg.emisor_rol === cfg.myRol && parseInt(msg.emisor_id) === cfg.myId);
        const sep = maybeDateSep(msg.fecha);
        if (sep) box.appendChild(sep);
        box.appendChild(buildBubble(msg, isMe));
        lastMsgId = Math.max(lastMsgId, parseInt(msg.id));
        if (scroll) scrollBottom(false);
    }

    function fetchSeen() {
        if (!hasPendingSeen || !cfg.convId) return;
        fetch(`${cfg.basePath}controladores/chat/visto.php?conv_id=${cfg.convId}&after_id=${lastSeenId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.ok || !data.max_seen_id) return;
                const maxId = data.max_seen_id;
                document.querySelectorAll('.chat-tick:not(.seen)').forEach(tick => {
                    if (parseInt(tick.dataset.msgId) <= maxId) tick.classList.add('seen');
                });
                lastSeenId = Math.max(lastSeenId, maxId);
                hasPendingSeen = document.querySelector('.chat-tick:not(.seen)') !== null;
            })
            .catch(() => {});
    }

    function schedulePoll() {
        clearTimeout(pollTimer);
        if (!cfg.convId) return;
        // Con la pestaña visible la conversación está a la vista: tope de 6s
        // para que los mensajes entrantes se sientan en tiempo real.
        const delay = document.hidden ? Math.max(pollInterval, POLL_HIDDEN_MS)
                                      : Math.min(pollInterval, 6000);
        pollTimer = setTimeout(fetchNew, delay);
    }

    function fetchNew() {
        if (!cfg.convId) return;
        fetch(`${cfg.basePath}controladores/chat/mensajes.php?conv_id=${cfg.convId}&after_id=${lastMsgId}`)
            .then(r => r.json())
            .then(data => {
                if (data.ok && data.messages.length) {
                    let gotIncoming = false;
                    data.messages.forEach(msg => {
                        const isMe = (msg.emisor_rol === cfg.myRol && parseInt(msg.emisor_id) === cfg.myId);
                        if (!isMe) gotIncoming = true;
                        appendMsg(msg);
                    });
                    if (gotIncoming) playSound('in');
                    // Activity: reset to minimum interval
                    pollInterval = POLL_MIN_MS;
                } else {
                    // No messages: gradually back off up to max
                    pollInterval = Math.min(Math.round(pollInterval * 1.4), POLL_MAX_MS);
                }
                fetchSeen();
                schedulePoll();
            })
            .catch(() => {
                // Error: back off to avoid hammering an overloaded server
                pollInterval = Math.min(pollInterval * 2, POLL_MAX_MS);
                schedulePoll();
            });
    }

    function sendMessage() {
        const input = el.input();
        const text = (input?.value || '').trim();
        if (!text) return;
        input.value = '';
        input.style.height = '';
        // Keep focus after sending
        input.focus();

        const fd = new FormData();
        fd.append('csrf_token', cfg.csrfToken);
        fd.append('conv_id',    cfg.convId);
        fd.append('contenido',  text);

        playSound('out');
        fetch(`${cfg.basePath}controladores/chat/enviar.php`, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.ok) {
                    appendMsg(data.message);
                    // Reset polling to fast interval to fetch any new msgs
                    pollInterval = POLL_MIN_MS;
                    clearTimeout(pollTimer);
                    fetchNew();
                }
            })
            .catch(() => {});
    }

    function setupInput() {
        const input = el.input();
        const btn   = el.sendBtn();
        if (!input) return;
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });
        input.addEventListener('input', () => {
            input.style.height = '';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        });
        if (btn) { btn.setAttribute('type', 'button'); btn.addEventListener('click', sendMessage); }
    }

    return {
        init(options) {
            cfg = { ...options };
            lastMsgId      = 0;
            lastDateLabel  = '';
            lastSeenId     = 0;
            hasPendingSeen = false;

            if (cfg.convId) {
                fetch(`${cfg.basePath}controladores/chat/mensajes.php?conv_id=${cfg.convId}`)
                    .then(r => r.json())
                    .then(data => { if (data.ok) renderAll(data.messages); })
                    .catch(() => {});
            }

            setupInput();

            if (cfg.convId) {
                pollInterval = POLL_MIN_MS;
                schedulePoll();

                // Catch up immediately when tab becomes visible again.
                document.addEventListener('visibilitychange', function onVisChange() {
                    if (!cfg.convId) { document.removeEventListener('visibilitychange', onVisChange); return; }
                    if (!document.hidden) {
                        pollInterval = POLL_MIN_MS;
                        clearTimeout(pollTimer);
                        fetchNew();
                    }
                });
            }

            // Focus automático al iniciar
            setTimeout(() => el.input()?.focus(), 200);
        },
    };
})();

// ── ChatModal ─────────────────────────────────────────────────────────────────
window.ChatModal = (function () {
    let cfg = {};
    let debounce = null;

    function roleLabel(rol) {
        if (rol === 'admin')      return 'Administrador';
        if (rol === 'profesor')   return 'Profesor';
        if (rol === 'tutor')      return 'Tutor';
        if (rol === 'estudiante') return 'Compañero';
        return rol;
    }

    function renderContacts(contacts) {
        const list = document.getElementById('chat-contact-list');
        if (!list) return;
        list.innerHTML = '';
        if (!contacts.length) {
            const emptyMsg = document.createElement('p');
            emptyMsg.style.cssText = 'color:var(--mut);font-size:.82rem;padding:10px 6px';
            emptyMsg.textContent = 'Sin resultados';
            list.appendChild(emptyMsg);
            return;
        }
        contacts.forEach(contact => {
            const div = document.createElement('div');
            div.className = 'chat-contact-item';

            const ava = document.createElement('div');
            ava.className = 'chat-ava ' + avaClass(contact.rol);
            ava.textContent = avaInit(contact.nombre);

            const info = document.createElement('div');

            const nameEl = document.createElement('div');
            nameEl.className = 'chat-contact-name';
            nameEl.textContent = contact.nombre;

            const roleEl = document.createElement('div');
            roleEl.className = 'chat-contact-role';
            roleEl.textContent = roleLabel(contact.rol);

            info.appendChild(nameEl);
            info.appendChild(roleEl);
            div.appendChild(ava);
            div.appendChild(info);
            div.addEventListener('click', () => startChat(contact));
            list.appendChild(div);
        });
    }

    function search(q) {
        fetch(`${cfg.basePath}controladores/chat/contactos.php?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => { if (data.ok) renderContacts(data.contacts); })
            .catch(() => {});
    }

    function startChat(contact) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = cfg.basePath + 'controladores/chat/iniciar.php';

        function hidden(name, value) {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = name;
            inp.value = value;
            return inp;
        }
        form.appendChild(hidden('csrf_token', cfg.csrfToken));
        form.appendChild(hidden('target_rol', contact.rol));
        form.appendChild(hidden('target_id',  contact.uid));
        document.body.appendChild(form);
        form.submit();
    }

    return {
        init(options) {
            cfg = options;
            const btn = document.getElementById('chat-new-btn');
            if (!btn) return;

            btn.addEventListener('click', () => {
                const overlay = document.getElementById('chat-modal-overlay');
                if (!overlay) return;
                overlay.style.display = 'flex';
                search('');
                document.getElementById('chat-modal-search')?.focus();
            });

            document.getElementById('chat-modal-close')?.addEventListener('click', () => {
                document.getElementById('chat-modal-overlay').style.display = 'none';
            });
            document.getElementById('chat-modal-overlay')?.addEventListener('click', e => {
                if (e.target === e.currentTarget) e.currentTarget.style.display = 'none';
            });
            document.getElementById('chat-modal-search')?.addEventListener('input', e => {
                clearTimeout(debounce);
                debounce = setTimeout(() => search(e.target.value), 300);
            });
        },
    };
})();

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
    const POLL_MS = 3000;

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

    function fetchNew() {
        if (!cfg.convId) return;
        fetch(`${cfg.basePath}controladores/chat/mensajes.php?conv_id=${cfg.convId}&after_id=${lastMsgId}`)
            .then(r => r.json())
            .then(data => {
                if (data.ok && data.messages.length) {
                    let gotIncoming = false;
                    data.messages.forEach(m => {
                        const isMe = (m.emisor_rol === cfg.myRol && parseInt(m.emisor_id) === cfg.myId);
                        if (!isMe) gotIncoming = true;
                        appendMsg(m);
                    });
                    if (gotIncoming) playSound('in');
                }
                fetchSeen();
            })
            .catch(() => {});
    }

    function sendMessage() {
        const input = el.input();
        const text = (input?.value || '').trim();
        if (!text) return;
        input.value = '';
        input.style.height = '';

        const fd = new FormData();
        fd.append('csrf_token', cfg.csrfToken);
        fd.append('conv_id',    cfg.convId);
        fd.append('contenido',  text);

        playSound('out');
        fetch(`${cfg.basePath}controladores/chat/enviar.php`, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.ok) appendMsg(data.message);
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
        btn?.addEventListener('click', sendMessage);
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
                clearInterval(pollTimer);
                pollTimer = setInterval(fetchNew, POLL_MS);
            }

            // Focus automático al iniciar
            setTimeout(() => el.input()?.focus(), 200);
        },
        destroy() {
            clearInterval(pollTimer);
            pollTimer = null;
        },
    };
})();

// ── ChatModal ─────────────────────────────────────────────────────────────────
window.ChatModal = (function () {
    let cfg = {};
    let debounce = null;

    function roleLabel(rol) {
        return rol === 'admin' ? 'Administrador' : rol === 'profesor' ? 'Profesor' : 'Estudiante';
    }

    function renderContacts(contacts) {
        const list = document.getElementById('chat-contact-list');
        if (!list) return;
        list.innerHTML = '';
        if (!contacts.length) {
            list.innerHTML = '<p style="color:var(--mut);font-size:.82rem;padding:10px 6px">Sin resultados</p>';
            return;
        }
        contacts.forEach(c => {
            const div = document.createElement('div');
            div.className = 'chat-contact-item';
            div.innerHTML = `
                <div class="chat-ava ${avaClass(c.rol)}">${avaInit(c.nombre)}</div>
                <div>
                    <div class="chat-contact-name">${c.nombre}</div>
                    <div class="chat-contact-role">${roleLabel(c.rol)}</div>
                </div>`;
            div.addEventListener('click', () => startChat(c));
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
        form.action = `${cfg.basePath}controladores/chat/iniciar.php`;
        form.innerHTML = `
            <input type="hidden" name="csrf_token" value="${cfg.csrfToken}">
            <input type="hidden" name="target_rol" value="${contact.rol}">
            <input type="hidden" name="target_id"  value="${contact.uid}">`;
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

/**
 * mensajes.js — Sistema de mensajería cliente.
 * Polling AJAX de mensajes no leídos + contadores de caracteres.
 * Requiere: jQuery (cargado en nav.php), toast.js (en footer).
 */
(function () {
    'use strict';

    var pollUrl   = window.MENSAJES_POLL_URL || '';
    var prevUnread = -1;

    // ── Polling ──────────────────────────────────────────────────────
    function poll() {
        if (!pollUrl) return;
        $.get(pollUrl)
            .done(function (data) {
                if (!data || !data.ok) return;
                actualizarBadge(data.unread);
                if (prevUnread >= 0 && data.unread > prevUnread) {
                    var nuevos = data.unread - prevUnread;
                    mostrarBannerNuevos(nuevos);
                    if (window.Toast) Toast.show(nuevos + ' mensaje(s) nuevo(s)', 'ok');
                }
                prevUnread = data.unread;
            });
    }

    function actualizarBadge(count) {
        document.querySelectorAll('.badge-mensajes').forEach(function (el) {
            el.textContent = count > 0 ? count : '';
            el.style.display = count > 0 ? '' : 'none';
        });
    }

    function mostrarBannerNuevos(count) {
        var existing = document.getElementById('msg-banner-nuevos');
        if (existing) existing.remove();

        var banner = document.createElement('div');
        banner.id = 'msg-banner-nuevos';
        banner.className = 'msg-banner-nuevos';
        banner.innerHTML =
            '<i class="fas fa-envelope"></i> Tienes <strong>' + count +
            '</strong> mensaje(s) nuevo(s). <a href="lista.php">Ver ahora</a>' +
            '<button class="msg-banner-cerrar" aria-label="Cerrar">×</button>';

        banner.querySelector('.msg-banner-cerrar').addEventListener('click', function () {
            banner.remove();
        });

        var anchor = document.querySelector('.panel, .cabecera');
        if (anchor) {
            anchor.parentNode.insertBefore(banner, anchor);
        } else {
            document.body.prepend(banner);
        }
    }

    // ── Contador de caracteres ────────────────────────────────────────
    function initCharCounters() {
        document.querySelectorAll('textarea[maxlength]').forEach(function (textarea) {
            var max = parseInt(textarea.getAttribute('maxlength'), 10);
            var counterEl = document.createElement('span');
            counterEl.className = 'msg-char-counter';
            textarea.parentNode.appendChild(counterEl);

            function update() {
                var len = textarea.value.length;
                counterEl.textContent = len + ' / ' + max;
                counterEl.classList.toggle('msg-char-limit', len > max * 0.88);
            }
            textarea.addEventListener('input', update);
            update();
        });
    }

    // ── Inline edit ──────────────────────────────────────────────────
    function initInlineEdit() {
        var editUrl = window.MSG_EDIT_URL || '';
        if (!editUrl) return;

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.msg-edit-btn');
            if (!btn) return;
            e.preventDefault();
            var row    = btn.closest('.msg-thread-row');
            var bubble = row && row.querySelector('.msg-thread-bubble');
            if (!bubble || bubble.classList.contains('editing')) return;

            var original = bubble.dataset.original || bubble.textContent;
            bubble.classList.add('editing');
            bubble.innerHTML =
                '<textarea class="msg-edit-ta" maxlength="1000">' +
                original.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') +
                '</textarea>' +
                '<div class="msg-edit-actions">' +
                  '<button type="button" class="msg-edit-save ibtn ibtn-primary"><i class="fas fa-check"></i> Guardar</button>' +
                  '<button type="button" class="msg-edit-cancel ibtn ibtn-secondary"><i class="fas fa-times"></i> Cancelar</button>' +
                '</div>';
            bubble.querySelector('.msg-edit-ta').focus();
            setTimeout(function() { row.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }, 50);
        });

        document.addEventListener('click', function (e) {
            // Cancel
            if (e.target.closest('.msg-edit-cancel')) {
                var bubble = e.target.closest('.msg-thread-bubble');
                if (!bubble) return;
                var original = bubble.dataset.original || '';
                bubble.classList.remove('editing');
                bubble.innerHTML = escHtmlMsg(original);
                return;
            }
            // Save
            if (e.target.closest('.msg-edit-save')) {
                var saveBtn = e.target.closest('.msg-edit-save');
                var bubble  = saveBtn.closest('.msg-thread-bubble');
                if (!bubble) return;
                var ta      = bubble.querySelector('.msg-edit-ta');
                var content = ta ? ta.value.trim() : '';
                if (!content) return;
                var row     = bubble.closest('.msg-thread-row');
                var msgId   = row ? row.dataset.msgId : null;
                if (!msgId) return;

                saveBtn.disabled = true;

                $.ajax({
                    url:     editUrl,
                    type:    'POST',
                    data:    {
                        csrf_token:    $('[name="modal_csrf"]').val(),
                        idReclamacion: msgId,
                        contenido:     content
                    },
                    dataType: 'json',
                    headers:  { 'X-Requested-With': 'XMLHttpRequest' }
                }).done(function (res) {
                    if (res && res.ok) {
                        bubble.dataset.original = content;
                        bubble.classList.remove('editing');
                        bubble.innerHTML = escHtmlMsg(content);
                        // Mostrar/actualizar el chip "(Editado)"
                        var foot = row.querySelector('.msg-thread-foot');
                        if (foot && !foot.querySelector('.msg-editado-chip')) {
                            var chip = document.createElement('span');
                            chip.className = 'msg-editado-chip';
                            chip.textContent = 'Editado';
                            var timeEl = foot.querySelector('.msg-thread-time');
                            if (timeEl && timeEl.nextSibling) {
                                foot.insertBefore(chip, timeEl.nextSibling);
                            } else {
                                foot.insertBefore(chip, foot.querySelector('.msg-edit-btn'));
                            }
                        }
                        if (window.Toast) Toast.show('Mensaje editado', 'success');
                    } else {
                        saveBtn.disabled = false;
                        if (window.Toast) Toast.show((res && res.msg) || 'Error al guardar', 'error');
                    }
                }).fail(function (jqXHR) {
                    saveBtn.disabled = false;
                    // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
                    if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
                    if (window.Toast) Toast.show('Error de conexión', 'error');
                });
            }
        });
    }

    function playMsgSound() {
        try {
            var ctx = new (window.AudioContext || window.webkitAudioContext)();
            [[660, 0, 0.15], [880, 0.15, 0.15]].forEach(function (note) {
                var osc  = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                osc.frequency.value = note[0];
                gain.gain.setValueAtTime(0, ctx.currentTime + note[1]);
                gain.gain.linearRampToValueAtTime(0.18, ctx.currentTime + note[1] + 0.02);
                gain.gain.linearRampToValueAtTime(0, ctx.currentTime + note[1] + note[2]);
                osc.start(ctx.currentTime + note[1]);
                osc.stop(ctx.currentTime + note[1] + note[2]);
            });
        } catch (e) {}
    }

    function escHtmlMsg(str) {
        return (window.AulaProUtils && window.AulaProUtils.escapeHtmlWithLineBreaks)
            ? window.AulaProUtils.escapeHtmlWithLineBreaks(str)
            : String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\n/g, '<br>');
    }

    // ── Init ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initCharCounters();
        initInlineEdit();
        if (pollUrl) {
            poll();
            setInterval(poll, 30000);
        }
    });

    window.playMsgSound = playMsgSound;
})();

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
                    var n = data.unread - prevUnread;
                    mostrarBannerNuevos(n);
                    if (window.Toast) Toast.show(n + ' mensaje(s) nuevo(s)', 'ok');
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

        var b = document.createElement('div');
        b.id = 'msg-banner-nuevos';
        b.className = 'msg-banner-nuevos';
        b.innerHTML =
            '<i class="fas fa-envelope"></i> Tienes <strong>' + count +
            '</strong> mensaje(s) nuevo(s). <a href="lista.php">Ver ahora</a>' +
            '<button class="msg-banner-cerrar" aria-label="Cerrar">×</button>';

        b.querySelector('.msg-banner-cerrar').addEventListener('click', function () {
            b.remove();
        });

        var anchor = document.querySelector('.panel, .cabecera');
        if (anchor) {
            anchor.parentNode.insertBefore(b, anchor);
        } else {
            document.body.prepend(b);
        }
    }

    // ── Contador de caracteres ────────────────────────────────────────
    function initCharCounters() {
        document.querySelectorAll('textarea[maxlength]').forEach(function (ta) {
            var max = parseInt(ta.getAttribute('maxlength'), 10);
            var ctr = document.createElement('span');
            ctr.className = 'msg-char-counter';
            ta.parentNode.appendChild(ctr);

            function update() {
                var n = ta.value.length;
                ctr.textContent = n + ' / ' + max;
                ctr.classList.toggle('msg-char-limit', n > max * 0.88);
            }
            ta.addEventListener('input', update);
            update();
        });
    }

    // ── Init ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        initCharCounters();
        if (pollUrl) {
            poll();
            setInterval(poll, 30000);
        }
    });
})();

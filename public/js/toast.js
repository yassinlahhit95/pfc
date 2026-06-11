/**
 * Toast notification system.
 * Reads existing .mensaje-exito / .mensaje-error divs already rendered by PHP,
 * hides them, and shows animated auto-dismiss toasts instead.
 * No external dependencies.
 */
(function () {
    'use strict';

    var DURATION = 4500; // ms before auto-dismiss

    var CSS = [
        '#toast-container{position:fixed;bottom:24px;right:24px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;}',
        '.toast{display:flex;align-items:center;gap:12px;min-width:280px;max-width:420px;padding:14px 18px;border-radius:12px;',
        'box-shadow:0 4px 20px rgba(0,0,0,.18);font-size:.9rem;font-weight:500;pointer-events:all;',
        'animation:toastIn .25s ease forwards;}',
        '.toast-ok{background:#ecfdf5;color:#065f46;border:1.5px solid #6ee7b7;}',
        '.toast-err{background:#fef2f2;color:#991b1b;border:1.5px solid #fca5a5;}',
        '.toast-icon{font-size:1.1rem;flex-shrink:0;}',
        '.toast-msg{flex:1;line-height:1.4;}',
        '.toast-close{background:none;border:none;cursor:pointer;font-size:1rem;opacity:.5;padding:0;line-height:1;flex-shrink:0;}',
        '.toast-close:hover{opacity:1;}',
        '.toast.removing{animation:toastOut .2s ease forwards;}',
        '@keyframes toastIn{from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);}}',
        '@keyframes toastOut{from{opacity:1;transform:translateX(0);}to{opacity:0;transform:translateX(40px);}}'
    ].join('');

    function injectCSS() {
        var s = document.createElement('style');
        s.textContent = CSS;
        document.head.appendChild(s);
    }

    function getOrCreateContainer() {
        var c = document.getElementById('toast-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'toast-container';
            document.body.appendChild(c);
        }
        return c;
    }

    function dismiss(el) {
        el.classList.add('removing');
        el.addEventListener('animationend', function () { el.remove(); });
    }

    function showToast(message, type) {
        var container = getOrCreateContainer();
        var toast = document.createElement('div');
        toast.className = 'toast ' + (type === 'ok' ? 'toast-ok' : 'toast-err');

        var icon = type === 'ok' ? '✓' : '✕';
        toast.innerHTML =
            '<span class="toast-icon">' + icon + '</span>' +
            '<span class="toast-msg">' + message + '</span>' +
            '<button class="toast-close" aria-label="Cerrar">✕</button>';

        toast.querySelector('.toast-close').addEventListener('click', function () {
            dismiss(toast);
        });

        container.appendChild(toast);

        setTimeout(function () {
            if (toast.parentNode) dismiss(toast);
        }, DURATION);
    }

    function init() {
        injectCSS();

        // Convert existing PHP flash message divs into toasts
        var selectors = [
            { sel: '.mensaje-exito', type: 'ok' },
            { sel: '.mensaje-error', type: 'err' }
        ];

        selectors.forEach(function (s) {
            document.querySelectorAll(s.sel).forEach(function (el) {
                var msg = el.textContent.trim();
                if (!msg) return;
                el.style.display = 'none'; // hide the inline div
                showToast(msg, s.type);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose for manual use: Toast.show('message', 'ok'|'err')
    window.Toast = { show: showToast };
})();

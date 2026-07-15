/**
 * Toast notification system.
 * Reads existing .mensaje-exito / .mensaje-error divs already rendered by PHP,
 * hides them, and shows animated auto-dismiss toasts instead.
 * No external dependencies. Accepts types: 'success'|'ok' → green, 'error'|'err' → red, 'info'|'warning' → blue/amber.
 */
(function () {
    'use strict';

    var DURATION = 4500;

    var CSS = [
        '#toast-container{position:fixed;bottom:24px;right:24px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none;}',
        '.toast{display:flex;align-items:flex-start;gap:12px;min-width:300px;max-width:420px;padding:14px 16px 20px;border-radius:14px;',
        'box-shadow:0 8px 32px rgba(0,0,0,.14);font-size:.92rem;font-weight:500;pointer-events:all;position:relative;overflow:hidden;',
        'animation:toastIn .32s cubic-bezier(.34,1.56,.64,1) forwards;}',
        '.toast-ok{background:#ecfdf5;color:#065f46;border:1.5px solid #6ee7b7;}',
        '.toast-err{background:#fef2f2;color:#991b1b;border:1.5px solid #fca5a5;}',
        '.toast-info{background:#eff6ff;color:#1e40af;border:1.5px solid #93c5fd;}',
        '.toast-warn{background:#fffbeb;color:#92400e;border:1.5px solid #fcd34d;}',
        '.toast-icon{font-size:1.15rem;flex-shrink:0;margin-top:1px;}',
        '.toast-ok .toast-icon{color:#10b981;}',
        '.toast-err .toast-icon{color:#ef4444;}',
        '.toast-info .toast-icon{color:#3b82f6;}',
        '.toast-warn .toast-icon{color:#f59e0b;}',
        '.toast-msg{flex:1;line-height:1.45;}',
        '.toast-close{background:none;border:none;cursor:pointer;font-size:.85rem;opacity:.4;padding:0;line-height:1;flex-shrink:0;margin-top:2px;color:inherit;}',
        '.toast-close:hover{opacity:1;}',
        '.toast-bar{position:absolute;bottom:0;left:0;height:3px;border-radius:0 0 14px 14px;}',
        '.toast-ok .toast-bar{background:#10b981;}',
        '.toast-err .toast-bar{background:#ef4444;}',
        '.toast-info .toast-bar{background:#3b82f6;}',
        '.toast-warn .toast-bar{background:#f59e0b;}',
        '.toast.removing{animation:toastOut .22s ease forwards;}',
        '@keyframes toastIn{from{opacity:0;transform:translateX(50px) scale(.9);}to{opacity:1;transform:translateX(0) scale(1);}}',
        '@keyframes toastOut{from{opacity:1;transform:translateX(0);}to{opacity:0;transform:translateX(50px);}}',
        '@media(max-width:700px){#toast-container{bottom:max(90px,calc(88px + env(safe-area-inset-bottom,0px)));right:12px;left:12px;align-items:stretch;}.toast{min-width:0;width:100%;}}'
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
        el.addEventListener('animationend', function () { el.remove(); }, { once: true });
    }

    function showToast(message, type) {
        var isOk   = (type === 'ok' || type === 'success');
        var isInfo = (type === 'info');
        var isWarn = (type === 'warning' || type === 'warn');
        var cls    = isOk ? 'toast-ok' : isInfo ? 'toast-info' : isWarn ? 'toast-warn' : 'toast-err';
        var container = getOrCreateContainer();
        var toast = document.createElement('div');
        toast.className = 'toast ' + cls;

        var iconClass = isOk ? 'fa-check-circle' : isInfo ? 'fa-info-circle' : isWarn ? 'fa-exclamation-triangle' : 'fa-exclamation-circle';
        toast.innerHTML =
            '<i class="fas ' + iconClass + ' toast-icon"></i>' +
            '<span class="toast-msg"></span>' +
            '<button class="toast-close" aria-label="Cerrar"><i class="fas fa-times"></i></button>' +
            '<span class="toast-bar"></span>';
        toast.querySelector('.toast-msg').textContent = message;

        var bar = toast.querySelector('.toast-bar');
        bar.style.cssText = 'width:100%;transition:width ' + DURATION + 'ms linear;';

        container.appendChild(toast);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () { bar.style.width = '0%'; });
        });

        var timer = setTimeout(function () {
            if (toast.parentNode) dismiss(toast);
        }, DURATION);

        toast.querySelector('.toast-close').addEventListener('click', function () {
            clearTimeout(timer);
            dismiss(toast);
        });
    }

    function init() {
        injectCSS();

        var selectors = [
            { sel: '.mensaje-exito', type: 'ok' },
            { sel: '.mensaje-error', type: 'err' }
        ];

        selectors.forEach(function (s) {
            document.querySelectorAll(s.sel).forEach(function (el) {
                var msg = el.textContent.trim();
                if (!msg) return;
                el.style.display = 'none';
                showToast(msg, s.type);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.Toast = { show: showToast };
})();

(function () {
    'use strict';

    var STORAGE_KEY = 'aulapro_cookie_consent';

    function getConsent() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    }

    function setConsent(status) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({ status: status, ts: Date.now() }));
        } catch (e) {}
    }

    // Scripts no esenciales (analytics, marketing...) deben marcarse:
    // <script type="text/plain" data-cookie-consent="non-essential" data-src="...">
    // Solo se activan tras aceptar; si se rechaza o no se decide, nunca se ejecutan.
    function activateNonEssentialScripts() {
        var pending = document.querySelectorAll('script[type="text/plain"][data-cookie-consent="non-essential"]');
        pending.forEach(function (oldScript) {
            var newScript = document.createElement('script');
            Array.prototype.forEach.call(oldScript.attributes, function (attr) {
                if (attr.name === 'type' || attr.name === 'data-cookie-consent') return;
                var name = attr.name === 'data-src' ? 'src' : attr.name;
                newScript.setAttribute(name, attr.value);
            });
            newScript.textContent = oldScript.textContent;
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
        document.dispatchEvent(new CustomEvent('cookieconsent:accepted'));
    }

    function hideBanner() {
        var banner = document.getElementById('cookie-banner');
        if (!banner) return;
        banner.classList.remove('cookie-banner-visible');
        setTimeout(function () { banner.remove(); }, 250);
    }

    function buildBanner() {
        if (document.getElementById('cookie-banner')) return;

        var banner = document.createElement('div');
        banner.id = 'cookie-banner';
        banner.setAttribute('role', 'dialog');
        banner.setAttribute('aria-live', 'polite');
        banner.setAttribute('aria-label', 'Aviso de cookies');
        banner.innerHTML =
            '<div class="cookie-banner-inner">' +
                '<p class="cookie-banner-text">' +
                    'Usamos cookies técnicas necesarias para el funcionamiento del sitio. ' +
                    'Con tu permiso, también podríamos usar cookies de análisis para mejorar el servicio. ' +
                    'Puedes aceptarlas o rechazarlas — el sitio funciona igual en ambos casos. ' +
                    'Más información en la <a href="/vistas/legal/politica-de-cookies.php">política de cookies</a>.' +
                '</p>' +
                '<div class="cookie-banner-actions">' +
                    '<button type="button" id="cookie-banner-reject" class="cookie-banner-btn cookie-banner-btn-secondary">Rechazar</button>' +
                    '<button type="button" id="cookie-banner-accept" class="cookie-banner-btn cookie-banner-btn-primary">Aceptar</button>' +
                '</div>' +
            '</div>';
        document.body.appendChild(banner);

        document.getElementById('cookie-banner-accept').addEventListener('click', function () {
            setConsent('accepted');
            hideBanner();
            activateNonEssentialScripts();
        });
        document.getElementById('cookie-banner-reject').addEventListener('click', function () {
            setConsent('rejected');
            hideBanner();
        });

        requestAnimationFrame(function () {
            banner.classList.add('cookie-banner-visible');
        });
    }

    function init() {
        var consent = getConsent();
        if (!consent || (consent.status !== 'accepted' && consent.status !== 'rejected')) {
            buildBanner();
            return;
        }
        if (consent.status === 'accepted') {
            activateNonEssentialScripts();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // API pública: reabrir el banner (p.ej. desde un enlace "Preferencias de cookies" en el footer)
    window.CookieConsent = {
        reopen: buildBanner,
        getStatus: function () {
            var c = getConsent();
            return c ? c.status : null;
        }
    };
})();

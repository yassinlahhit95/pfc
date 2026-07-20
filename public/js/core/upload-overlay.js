/* ══════════════════════════════════════════════════════════════════════
   UPLOAD OVERLAY — public/js/core/upload-overlay.js
   Bloquea la página y difumina el fondo mientras se sube un archivo
   (imagen/vídeo), en formularios normales y en subidas AJAX.
   Uso: window.UploadOverlay.show('Subiendo...'); ... .hide();
   ══════════════════════════════════════════════════════════════════════ */
(function () {
    'use strict';

    var $overlay = null;

    function crearOverlay() {
        var el = document.createElement('div');
        el.className = 'upload-overlay';
        el.setAttribute('aria-hidden', 'true');
        el.innerHTML =
            '<div class="upload-overlay-caja">' +
                '<div class="upload-overlay-spinner"><i class="fas fa-circle-notch fa-spin"></i></div>' +
                '<p class="upload-overlay-msg"></p>' +
            '</div>';
        document.body.appendChild(el);
        return el;
    }

    window.UploadOverlay = {
        show: function (mensaje) {
            if (!$overlay) $overlay = crearOverlay();
            $overlay.querySelector('.upload-overlay-msg').textContent = mensaje || 'Subiendo archivo...';
            document.body.classList.add('upload-overlay-activo');
            $overlay.classList.add('visible');
        },
        hide: function () {
            if (!$overlay) return;
            $overlay.classList.remove('visible');
            document.body.classList.remove('upload-overlay-activo');
        }
    };
}());

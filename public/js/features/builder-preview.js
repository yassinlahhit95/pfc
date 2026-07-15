// Script inyectado en el iframe del constructor (cuando ?preview=1)
(function() {
    'use strict';

    // Highlight activo temporal
    let activeHighlight = null;

    // Escuchar mensajes del padre (el constructor)
    window.addEventListener('message', function(e) {
        // Solo aceptar mensajes del mismo origen (el constructor va en el mismo dominio)
        if (e.origin !== window.location.origin) return;
        if (!e.data || typeof e.data !== 'object') return;

        if (e.data.action === 'highlight_section') {
            const id = e.data.idSeccion;
            const target = document.querySelector('[data-lb-id="' + id + '"]');
            
            if (target) {
                // Scroll suave a la sección
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Añadir highlight
                if (activeHighlight) {
                    activeHighlight.classList.remove('lp-highlight-preview');
                }
                target.classList.add('lp-highlight-preview');
                activeHighlight = target;
            }
        }
    });

    // Interceptar clicks en la previsualización para abrir el editor en el padre
    document.addEventListener('click', function(e) {
        // Encontrar si hicimos click dentro de una sección editable
        const sec = e.target.closest('[data-lb-id]');
        
        if (sec) {
            e.preventDefault();
            e.stopPropagation();

            const idSeccion = sec.getAttribute('data-lb-id');

            // Enviar mensaje al padre
            window.parent.postMessage({
                action: 'edit_section',
                idSeccion: parseInt(idSeccion, 10)
            }, window.location.origin);

            // Highlight visual inmediato
            if (activeHighlight) {
                activeHighlight.classList.remove('lp-highlight-preview');
            }
            sec.classList.add('lp-highlight-preview');
            activeHighlight = sec;
        }
    }, true); // Capturar en la fase de captura para evitar que enlaces sigan su curso
})();

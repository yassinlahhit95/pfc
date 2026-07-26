// Script inyectado en el iframe del constructor (cuando ?preview=1)
(function() {
    'use strict';

    // Highlight de sección activo temporal
    let activeHighlight = null;

    // Edición en línea activa (texto/textarea) — como mucho una a la vez.
    let editingEl = null;
    let editingSnapshot = '';
    let editingKeydown = null;

    function resaltarSeccion(target) {
        if (activeHighlight && activeHighlight !== target) {
            activeHighlight.classList.remove('lp-highlight-preview');
        }
        if (target) {
            target.classList.add('lp-highlight-preview');
            activeHighlight = target;
        }
    }

    // Confirma la edición en curso: si el texto cambió, avisa al padre para
    // que lo guarde; si no, no hace ninguna petición de más.
    function confirmarEdicion() {
        if (!editingEl) return;
        const el = editingEl;
        const idSeccion = el.closest('[data-lb-id]');
        const valor = el.innerText.trim();

        limpiarEdicion();

        if (idSeccion && valor !== editingSnapshot) {
            // '*' y no window.location.origin: este documento se cargó vía
            // iframe.srcdoc, así que tiene un origen opaco — window.location.origin
            // aquí es literalmente el string "null", y postMessage(msg, "null")
            // lanza SyntaxError ("Invalid target origin") en vez de enviar nada.
            // El postMessage entero fallaba en silencio (la excepción quedaba
            // sin capturar dentro del manejador de blur) y ninguna edición en
            // línea llegaba jamás al padre — de ahí que nada se autoguardara.
            window.parent.postMessage({
                action: 'field_edited',
                idSeccion: parseInt(idSeccion.getAttribute('data-lb-id'), 10),
                field: el.getAttribute('data-lb-field'),
                value: valor
            }, '*');
        }
    }

    // Cancela la edición en curso (Escape): restaura el texto original sin guardar.
    function cancelarEdicion() {
        if (!editingEl) return;
        editingEl.innerText = editingSnapshot;
        limpiarEdicion();
    }

    function limpiarEdicion() {
        if (!editingEl) return;
        editingEl.removeEventListener('keydown', editingKeydown);
        editingEl.contentEditable = 'false';
        editingEl.classList.remove('lp-editando-campo');
        editingEl = null;
        editingSnapshot = '';
        editingKeydown = null;
    }

    function iniciarEdicionTexto(fieldEl, kind) {
        if (editingEl === fieldEl) return; // ya se está editando este mismo nodo
        if (editingEl) confirmarEdicion(); // termina la edición anterior antes de empezar otra

        editingEl = fieldEl;
        editingSnapshot = fieldEl.innerText.trim();
        fieldEl.contentEditable = 'true';
        fieldEl.classList.add('lp-editando-campo');
        fieldEl.focus();

        editingKeydown = function(ev) {
            if (ev.key === 'Escape') {
                ev.preventDefault();
                cancelarEdicion();
            } else if (ev.key === 'Enter' && kind === 'text') {
                // Los campos de una sola línea confirman con Enter en vez de
                // insertar un salto de línea; los de tipo "textarea" sí lo permiten.
                ev.preventDefault();
                fieldEl.blur();
            }
        };
        fieldEl.addEventListener('keydown', editingKeydown);
        fieldEl.addEventListener('blur', confirmarEdicion, { once: true });
    }

    // Escuchar mensajes del padre (el constructor)
    window.addEventListener('message', function(e) {
        // Se comprueba la ventana emisora (e.source), no e.origin: este
        // documento tiene un origen opaco (srcdoc), así que su propio
        // window.location.origin es el string "null" — comparar e.origin
        // (el origen real del padre) contra eso nunca puede coincidir, y el
        // mensaje se descartaría siempre. Comprobar que viene exactamente de
        // nuestra propia ventana padre es una verificación más fiable aquí,
        // no un debilitamiento del filtro anterior.
        if (e.source !== window.parent) return;
        if (!e.data || typeof e.data !== 'object') return;

        if (e.data.action === 'highlight_section') {
            const id = e.data.idSeccion;
            const target = document.querySelector('[data-lb-id="' + id + '"]');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                resaltarSeccion(target);
            }
        }
    });

    // Interceptar clicks en la previsualización.
    document.addEventListener('click', function(e) {
        // 1) Campo editable en línea (texto/textarea/imagen) — más específico,
        //    se comprueba antes que la sección entera.
        const fieldEl = e.target.closest('[data-lb-field]');
        if (fieldEl) {
            const sec = fieldEl.closest('[data-lb-id]');
            if (!sec) return;
            const idSeccion = sec.getAttribute('data-lb-id');
            const field = fieldEl.getAttribute('data-lb-field');
            const kind = fieldEl.getAttribute('data-lb-kind') || 'text';

            // Dentro de un <summary> (acordeón de FAQ), el click también abre/cierra
            // el <details> de forma nativa — no se cancela ese comportamiento, así
            // el admin puede desplegar la respuesta para poder editarla.
            const enResumen = !!e.target.closest('summary');
            if (!enResumen) e.preventDefault();
            e.stopPropagation();

            if (kind === 'imagen') {
                // '*': ver la nota en confirmarEdicion() sobre el origen opaco
                // de este documento (srcdoc).
                window.parent.postMessage({
                    action: 'edit_image',
                    idSeccion: parseInt(idSeccion, 10),
                    field: field
                }, '*');
            } else {
                iniciarEdicionTexto(fieldEl, kind);
            }

            resaltarSeccion(sec);
            return;
        }

        // 2) El resto de la sección (fuera de cualquier campo marcado): abre el
        //    panel lateral completo, comportamiento existente sin cambios.
        const sec = e.target.closest('[data-lb-id]');
        if (sec) {
            e.preventDefault();
            e.stopPropagation();

            if (editingEl) confirmarEdicion();

            // '*': ver la nota en confirmarEdicion() sobre el origen opaco de
            // este documento (srcdoc).
            window.parent.postMessage({
                action: 'edit_section',
                idSeccion: parseInt(sec.getAttribute('data-lb-id'), 10)
            }, '*');

            resaltarSeccion(sec);
        }
    }, true); // Capturar en la fase de captura para evitar que enlaces sigan su curso
})();

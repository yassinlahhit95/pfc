// Función para sacar un aviso en pantalla (toast) premium
export function avisoPush(t, m, tipo = 'info') {
    var c = document.getElementById('contenedor-notificaciones');

    if (!c) {
        c = document.createElement('div');
        c.id = 'contenedor-notificaciones';
        document.body.appendChild(c);
    }

    var div = document.createElement('div');
    div.className = "notificacion-toast premium " + tipo;

    var icon = 'fa-bell';
    if (tipo === 'exito') icon = 'fa-check-circle';
    if (tipo === 'error') icon = 'fa-exclamation-triangle';

    div.innerHTML = `
        <div class="toast-icono">
            <i class="fas ${icon}"></i>
        </div>
        <div class="toast-contenido">
            <div class="toast-titulo">${t}</div>
            <div class="toast-mensaje">${m}</div>
        </div>
        <button class="toast-cerrar" aria-label="Cerrar">&times;</button>
        <div class="toast-progreso">
            <div class="toast-progreso-barra"></div>
        </div>
    `;

    c.appendChild(div);

    // Sonido opcional (solo si el usuario ha interactuado)
    var sn = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
    sn.volume = 0.7;
    sn.play().catch(function() { /* Silenciar error si no hay interacción previa */ });

    div.querySelector('.toast-cerrar').onclick = function() {
        quitarToast(div);
    };

    // Auto-dismiss tras 6 segundos
    setTimeout(function() {
        quitarToast(div);
    }, 6000);
}

function quitarToast(el) {
    if (!el || !el.parentNode) return;
    el.classList.add('desvanecer');
    el.addEventListener('animationend', function() {
        if (el.parentNode) el.remove();
    }, { once: true });
}

// Función para sacar un aviso en pantalla (toast) premium
export function avisoPush(titulo, mensaje, tipo = 'info') {
    var contenedor = document.getElementById('contenedor-notificaciones');

    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'contenedor-notificaciones';
        document.body.appendChild(contenedor);
    }

    var toastEl = document.createElement('div');
    toastEl.className = "notificacion-toast premium " + tipo;

    var icon = 'fa-bell';
    if (tipo === 'exito') icon = 'fa-check-circle';
    if (tipo === 'error') icon = 'fa-exclamation-triangle';

    // Static structure via innerHTML is safe here — icon is hardcoded from tipo, not user input.
    // Title and body are set via .textContent below to prevent XSS from FCM payload content.
    toastEl.innerHTML = `
        <div class="toast-icono"><i class="fas ${icon}"></i></div>
        <div class="toast-contenido">
            <div class="toast-titulo"></div>
            <div class="toast-mensaje"></div>
        </div>
        <button class="toast-cerrar" aria-label="Cerrar">&times;</button>
        <div class="toast-progreso"><div class="toast-progreso-barra"></div></div>
    `;
    toastEl.querySelector('.toast-titulo').textContent = titulo;
    toastEl.querySelector('.toast-mensaje').textContent = mensaje;

    contenedor.appendChild(toastEl);

    // Sonido opcional (solo si el usuario ha interactuado)
    var sonido = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
    sonido.volume = 0.7;
    sonido.play().catch(function() { /* Silenciar error si no hay interacción previa */ });

    toastEl.querySelector('.toast-cerrar').onclick = function() {
        quitarToast(toastEl);
    };

    // Auto-dismiss tras 6 segundos
    setTimeout(function() {
        quitarToast(toastEl);
    }, 6000);
}

function quitarToast(el) {
    if (!el || !el.parentNode) return;
    el.classList.add('desvanecer');
    el.addEventListener('animationend', function() {
        if (el.parentNode) el.remove();
    }, { once: true });
}

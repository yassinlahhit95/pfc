// Función para sacar un aviso en pantalla (toast)
export function avisoPush(t, m, tipo = 'info') {
    var c = document.getElementById('contenedor-notificaciones');

    if (!c) {
        c = document.createElement('div');
        c.id = 'contenedor-notificaciones';
        document.body.appendChild(c);
    }

    var div = document.createElement('div');
    div.className = "notificacion-toast " + tipo;

    var icon = 'fa-bell';
    if (tipo === 'exito') icon = 'fa-check-circle';
    if (tipo === 'error') icon = 'fa-exclamation-circle';

    div.innerHTML = `
        <div class="toast-icono">
            <i class="fas ${icon}"></i>
        </div>
        <div class="toast-contenido">
            <div class="toast-titulo">${t}</div>
            <div class="toast-mensaje">${m}</div>
        </div>
        <button class="toast-cerrar">&times;</button>
        <div class="toast-progreso">
            <div class="toast-progreso-barra"></div>
        </div>
    `;

    c.appendChild(div);

    // Sonido opcional
    var sn = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
    sn.play().catch(function() {});

    div.querySelector('.toast-cerrar').onclick = function() {
        quitarToast(div);
    };

    setTimeout(function() {
        quitarToast(div);
    }, 5000);
}

function quitarToast(el) {
    el.classList.add('desvanecer');
    setTimeout(function() {
        if (el.parentNode) {
            el.parentNode.removeChild(el);
        }
    }, 300);
}

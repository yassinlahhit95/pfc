export function mostrarNotificacionUI(titulo, mensaje, tipo = 'info') {
    let contenedor = document.getElementById('contenedor-notificaciones');
    
    // Si no existe el contenedor, lo creamos
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'contenedor-notificaciones';
        document.body.appendChild(contenedor);
    }

    const toast = document.createElement('div');
    toast.className = `notificacion-toast ${tipo}`;
    
    let icono = 'fa-bell';
    if (tipo === 'exito') icono = 'fa-check-circle';
    if (tipo === 'error') icono = 'fa-exclamation-circle';

    toast.innerHTML = `
        <div class="toast-icono">
            <i class="fas ${icono}"></i>
        </div>
        <div class="toast-contenido">
            <div class="toast-titulo">${titulo}</div>
            <div class="toast-mensaje">${mensaje}</div>
        </div>
        <button class="toast-cerrar">&times;</button>
        <div class="toast-progreso">
            <div class="toast-progreso-barra"></div>
        </div>
    `;

    contenedor.appendChild(toast);

    // Sonido de notificación (opcional)
    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2358/2358-preview.mp3');
    audio.play().catch(() => {}); // Ignorar si el navegador bloquea el auto-play

    // Evento cerrar manual
    toast.querySelector('.toast-cerrar').onclick = () => {
        cerrarToast(toast);
    };

    // Auto cerrar después de 5 segundos
    setTimeout(() => {
        cerrarToast(toast);
    }, 5000);
}

function cerrarToast(toast) {
    toast.classList.add('desvanecer');
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 300);
}

/**
 * Analytics - Tracking de eventos en Aula Digital
 */

class AulaAnalytics {
    constructor(idUsuario, tipoUsuario, idModulo) {
        this.idUsuario = idUsuario;
        this.tipoUsuario = tipoUsuario;
        this.idModulo = idModulo;
        this.sessionStart = Date.now();
        
        // Determine the root path relative to this script (public/js/core/analytics.js is
        // 3 directories below the project root; see CLAUDE.md's note on path-depth-sensitive files).
        const scriptUrl = new URL(document.currentScript ? document.currentScript.src : window.location.origin + '/public/js/core/analytics.js');
        this.appRoot = new URL('../../../', scriptUrl).pathname;
        
        this.init();
    }

    init() {
        this.setupDownloadTracking();
        this.setupUploadTracking();
        this.setupViewTracking();
        this.setupEntregaTracking();
        this.setupSessionTracking();
    }

    /**
     * Registrar descargas de archivos
     */
    setupDownloadTracking() {
        document.querySelectorAll('a[download]').forEach(link => {
            link.addEventListener('click', (e) => {
                const nombreArchivo = link.textContent.trim() || 'archivo';
                const idArchivo = link.getAttribute('data-id-archivo') || 0;
                this.track('descargar', {
                    nombreArchivo,
                    idArchivo
                });
            });
        });
    }

    /**
     * Registrar vistas de archivos (modal)
     */
    setupViewTracking() {
        document.querySelectorAll('[data-ver-archivo]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const archivo = btn.getAttribute('data-nombre') || 'archivo';
                const idArchivo = btn.getAttribute('data-id-archivo') || 0;
                this.track('ver', {
                    archivo,
                    idArchivo,
                    tipo: btn.getAttribute('data-ext')
                });
            });
        });
    }

    /**
     * Registrar subidas de archivos
     */
    setupUploadTracking() {
        const formSubir = document.getElementById('formSubir');
        if (formSubir) {
            formSubir.addEventListener('submit', (e) => {
                const fileInput = formSubir.querySelector('input[name="archivos[]"]');
                const archivos = fileInput?.files?.length || 0;
                this.track('subir', {
                    cantidad: archivos,
                    totalSize: this.calcularTamanioTotal(fileInput?.files)
                });
            });
        }
    }

    /**
     * Registrar entregas de tareas
     */
    setupEntregaTracking() {
        document.querySelectorAll('form[action*="enviarEntrega"]').forEach(form => {
            form.addEventListener('submit', (e) => {
                const idTarea = form.querySelector('input[name="idTarea"]')?.value;
                this.track('entrega', {
                    idTarea,
                    tiempoEdicion: this.getEditTime(form)
                });
            });
        });
    }

    /**
     * Registrar sesión de usuario
     */
    setupSessionTracking() {
        window.addEventListener('beforeunload', (e) => {
            const duracionSesion = Math.round((Date.now() - this.sessionStart) / 1000);
            this.track('session_end', {
                duracion: duracionSesion
            });
        });
    }

    /**
     * Método genérico para registrar evento
     */
    track(accion, metadatos = {}) {
        const payload = {
            idUsuario: this.idUsuario,
            tipoUsuario: this.tipoUsuario,
            accion: accion,
            idModulo: this.idModulo,
            metadatos: metadatos,
            timestamp: new Date().toISOString()
        };

        // Enviar a servidor (asíncrono, no bloquea)
        fetch(this.appRoot + 'controladores/comunes/registrarAnalytics.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        }).catch(() => {
            // Silenciar errores de red
        });
    }

    /**
     * Calcular tamaño total de archivos
     */
    calcularTamanioTotal(files) {
        if (!files) return 0;
        let total = 0;
        for (let i = 0; i < files.length; i++) {
            total += files[i].size;
        }
        return Math.round(total / 1024 / 1024 * 100) / 100; // MB
    }

    /**
     * Obtener tiempo de edición de formulario
     */
    getEditTime(form) {
        const startTime = form.dataset.startTime || Date.now();
        return Math.round((Date.now() - startTime) / 1000);
    }

    /**
     * Eventos personalizados
     */
    trackBusqueda(query) {
        this.track('busqueda', { query });
    }

    trackPaginacion(pagina, total) {
        this.track('paginacion', { pagina, total });
    }

    trackTabSwitch(tabName) {
        this.track('tab_switch', { tab: tabName });
    }

    trackModalOpen(modalName) {
        this.track('modal_open', { modal: modalName });
    }

    trackTemaChange(tema) {
        this.track('tema_change', { tema });
    }
}

// Inicializar (requiere variables globales: idUsuario, tipoUsuario, idModulo)
document.addEventListener('DOMContentLoaded', () => {
    if (typeof idUsuario !== 'undefined' && typeof tipoUsuario !== 'undefined') {
        window.analytics = new AulaAnalytics(
            window.idUsuario,
            window.tipoUsuario,
            window.idModulo || 0
        );
    }
});

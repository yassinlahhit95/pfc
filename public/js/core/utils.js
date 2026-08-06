/**
 * Utilidades compartidas de AulaPro — evita implementaciones redundantes entre módulos.
 * Se carga primero en footer.php, antes que el resto de scripts core/feature.
 */
(function(window) {
  window.AulaProUtils = {
    /**
     * Escapa caracteres especiales HTML para prevenir XSS.
     * Seguro tanto para contenido de texto como para atributos HTML.
     */
    escapeHtml: function(str) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      };
      return String(str || '').replace(/[&<>"']/g, c => map[c]);
    },

    /**
     * Escapa HTML y conserva los saltos de línea como etiquetas <br>.
     * Usado al mostrar mensajes, comentarios, etc. donde los saltos de línea deben verse.
     */
    escapeHtmlWithLineBreaks: function(str) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
      };
      return String(str || '').replace(/[&<>"']/g, c => map[c]).replace(/\n/g, '<br>');
    },

    /**
     * Obtiene el token CSRF a partir de los selectores de input habituales.
     * Comprueba: [name="csrf_token"], [name="modal_csrf"], #csrf-token
     */
    getCSRFToken: function() {
      const selectors = ['[name="csrf_token"]', '[name="modal_csrf"]', '#csrf-token'];
      for (let sel of selectors) {
        const el = document.querySelector(sel);
        if (el && el.value) return el.value;
      }
      return '';
    },

    /**
     * Resuelve la ruta raíz de la app a partir de la ubicación del script actual.
     * Usado por notificaciones-dashboard.js, chat-widget.js, etc.
     * para construir URLs relativas desde módulos que pueden estar 3 o más niveles de profundidad.
     */
    resolveAppPath: function(relativePath) {
      const script = document.currentScript || Array.from(document.scripts).pop();
      if (!script) return relativePath;

      const scriptPath = script.src;
      const depth = (scriptPath.match(/\//g) || []).length - (window.location.pathname.match(/\//g) || []).length;
      const prefix = '../'.repeat(Math.max(0, depth - 1));

      return (prefix + relativePath).replace(/\.\.\//g, (m, offset) => {
        return offset === 0 ? m : '';
      });
    }
  };
})(window);

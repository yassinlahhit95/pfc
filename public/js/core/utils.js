/**
 * AulaPro Shared Utilities — prevents redundant implementations across modules.
 * Loaded first in footer.php before other core/feature scripts.
 */
(function(window) {
  window.AulaProUtils = {
    /**
     * Escape HTML special characters to prevent XSS.
     * Safe for use in both text content and HTML attributes.
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
     * Retrieve CSRF token from common input selectors.
     * Checks: [name="csrf_token"], [name="modal_csrf"], #csrf-token
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
     * Resolve app root path from current script location.
     * Used by notificaciones-dashboard.js, chat-widget.js, etc.
     * to construct relative URLs from modules that can be 3+ levels deep.
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

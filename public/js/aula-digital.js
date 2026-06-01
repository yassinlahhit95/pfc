/**
 * AULA DIGITAL - JavaScript Utilities
 * Sesiones Vivas, Tareas y Entregas
 */

document.addEventListener('DOMContentLoaded', function() {
  initMenuToggle();
  initFormValidation();
  initTableSearch();
});

/**
 * Toggle Mobile Menu
 */
function initMenuToggle() {
  const menuToggle = document.querySelector('.menu-toggle');
  const barraLateral = document.getElementById('barraLateral');

  if (!menuToggle) return;

  menuToggle.addEventListener('click', function() {
    barraLateral.classList.toggle('activo');
  });

  // Close menu on link click
  document.querySelectorAll('.enlace-menu').forEach(link => {
    link.addEventListener('click', function() {
      if (window.innerWidth <= 768) {
        barraLateral.classList.remove('activo');
      }
    });
  });
}

/**
 * Form Validation
 */
function initFormValidation() {
  const forms = document.querySelectorAll('form');

  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      let isValid = true;
      let errorMessages = [];

      // Check required fields
      this.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          errorMessages.push(`${field.labels[0]?.textContent || field.name} es requerido`);
          field.classList.add('error');
        } else {
          field.classList.remove('error');
        }
      });

      // Email validation
      this.querySelectorAll('input[type="email"]').forEach(field => {
        if (field.value && !isValidEmail(field.value)) {
          isValid = false;
          errorMessages.push('Email inválido');
          field.classList.add('error');
        }
      });

      // File size validation
      this.querySelectorAll('input[type="file"]').forEach(field => {
        if (field.files.length > 0) {
          const maxSize = parseInt(field.getAttribute('data-max-size') || '10485760'); // 10MB default
          for (let i = 0; i < field.files.length; i++) {
            const file = field.files[i];
            if (file.size > maxSize) {
              isValid = false;
              const fileName = field.files.length > 1 ? `(${file.name}) ` : '';
              errorMessages.push(`${fileName}Archivo muy grande. Máximo: ${formatFileSize(maxSize)}`);
              field.classList.add('error');
              break;
            }
          }
        }
      });

      if (!isValid) {
        e.preventDefault();
        showError(errorMessages.join('<br>'));
        window.scrollTo(0, 0);
      }
    });
  });
}

/**
 * Table Search/Filter
 */
function initTableSearch() {
  const searchInputs = document.querySelectorAll('[data-search]');

  searchInputs.forEach(input => {
    input.addEventListener('keyup', function() {
      const searchTerm = this.value.toLowerCase();
      const tableId = this.getAttribute('data-search');
      const table = document.getElementById(tableId);

      if (!table) return;

      table.querySelectorAll('tbody tr').forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
      });
    });
  });
}

/**
 * Utilities
 */

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

function showError(message) {
  const errorDiv = document.createElement('div');
  errorDiv.className = 'mensaje-error';
  errorDiv.innerHTML = message;

  const mainContent = document.querySelector('.contenido-principal');
  if (mainContent) {
    mainContent.insertBefore(errorDiv, mainContent.firstChild);
    setTimeout(() => errorDiv.remove(), 5000);
  }
}

function showSuccess(message) {
  const successDiv = document.createElement('div');
  successDiv.className = 'mensaje-exito';
  successDiv.innerHTML = message;

  const mainContent = document.querySelector('.contenido-principal');
  if (mainContent) {
    mainContent.insertBefore(successDiv, mainContent.firstChild);
    setTimeout(() => successDiv.remove(), 5000);
  }
}

/**
 * Download Handler
 */
function downloadFile(url, filename) {
  const link = document.createElement('a');
  link.href = url;
  link.download = filename || 'descarga';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    showSuccess('Copiado al portapapeles');
  }).catch(() => {
    showError('Error al copiar');
  });
}

/**
 * Date Formatting
 */
function formatDate(dateString) {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

function formatDateTime(dateString) {
  const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('es-ES', options);
}

/**
 * Table Row Selection
 */
function enableRowSelection() {
  const checkboxes = document.querySelectorAll('input[type="checkbox"][data-row-check]');

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const row = this.closest('tr');
      if (this.checked) {
        row.classList.add('selected');
      } else {
        row.classList.remove('selected');
      }
    });
  });
}

/**
 * Modal/Dialog Handling
 */
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.add('activo');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove('activo');
    document.body.style.overflow = 'auto';
  }
}

/**
 * Confirm Dialog
 */
function confirm(message, onConfirm, onCancel) {
  if (window.confirm(message)) {
    onConfirm && onConfirm();
  } else {
    onCancel && onCancel();
  }
}

/**
 * Loading State
 */
function setLoading(element, isLoading) {
  if (isLoading) {
    element.disabled = true;
    element.classList.add('loading');
    element.innerHTML = '<span class="spinner"></span> Procesando...';
  } else {
    element.disabled = false;
    element.classList.remove('loading');
  }
}

/**
 * Export functions globally
 */
window.AulaDigital = {
  openModal,
  closeModal,
  confirm,
  setLoading,
  showError,
  showSuccess,
  downloadFile,
  copyToClipboard,
  formatDate,
  formatDateTime,
  enableRowSelection,
  isValidEmail,
  formatFileSize
};

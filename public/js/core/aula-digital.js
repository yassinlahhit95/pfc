// Utilidades generales de formularios de panel: validación de required/email/
// tamaño de archivo antes de enviar, búsqueda de tabla por data-search, y
// helpers de mensaje/portapapeles expuestos en window.AulaDigital.
document.addEventListener('DOMContentLoaded', function() {
    initFormValidation();
    initTableSearch();
});
// El toggle del sidebar lo gestiona menu.js (toggleMenu); aqui se eliminó
// el duplicado que provocaba un doble toggle de la clase .activo.

// ── Validación de formularios ───────────────────────────────────────────────
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            let errorMessages = [];
            this.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    errorMessages.push(`${field.labels[0]?.textContent || field.name} es requerido`);
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });
            this.querySelectorAll('input[type="email"]').forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    isValid = false;
                    errorMessages.push('Email inválido');
                    field.classList.add('error');
                }
            });
            this.querySelectorAll('input[type="file"]').forEach(field => {
                if (field.files.length > 0) {
                    const maxSize = parseInt(field.getAttribute('data-max-size') || '10485760');
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

// ── Búsqueda de tabla ────────────────────────────────────────────────────────
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

// ── Helpers ──────────────────────────────────────────────────────────────────
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
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showSuccess('Copiado al portapapeles');
    }).catch(() => {
        showError('Error al copiar');
    });
}

window.AulaDigital = {
    showError,
    showSuccess,
    copyToClipboard,
    isValidEmail,
    formatFileSize
};

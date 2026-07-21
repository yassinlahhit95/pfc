// Utilidades generales de formularios de panel: validación de required/email/
// tamaño de archivo antes de enviar, y helpers de mensaje/portapapeles
// expuestos en window.AulaDigital.
document.addEventListener('DOMContentLoaded', function() {
    initFormValidation();
});

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
                showError(errorMessages.join(', '));
                window.scrollTo(0, 0);
            }
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
    const base = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const unitIndex = Math.floor(Math.log(bytes) / Math.log(base));
    return Math.round(bytes / Math.pow(base, unitIndex) * 100) / 100 + ' ' + sizes[unitIndex];
}
function showError(message) {
    if (window.Toast) window.Toast.show(message, 'error');
}
function showSuccess(message) {
    if (window.Toast) window.Toast.show(message, 'success');
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

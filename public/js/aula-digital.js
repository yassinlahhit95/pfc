document.addEventListener('DOMContentLoaded', function() {
 initFormValidation();
 initTableSearch();
});
// El toggle del sidebar lo gestiona menu.js (toggleMenu); aqui se eliminó
// el duplicado que provocaba un doble toggle de la clase .activo.
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
function downloadFile(url, filename) {
 const link = document.createElement('a');
 link.href = url;
 link.download = filename || 'descarga';
 document.body.appendChild(link);
 link.click();
 document.body.removeChild(link);
}
function copyToClipboard(text) {
 navigator.clipboard.writeText(text).then(() => {
 showSuccess('Copiado al portapapeles');
 }).catch(() => {
 showError('Error al copiar');
 });
}
function formatDate(dateString) {
 const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
 return new Date(dateString).toLocaleDateString('es-ES', options);
}
function formatDateTime(dateString) {
 const options = { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' };
 return new Date(dateString).toLocaleDateString('es-ES', options);
}
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
function confirmAction(message, onConfirm, onCancel) {
 if (window.confirm(message)) {
 onConfirm && onConfirm();
 } else {
 onCancel && onCancel();
 }
}
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
window.AulaDigital = {
 openModal,
 closeModal,
 confirm: confirmAction,
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
// ========================================
// MODAL DE CONFIRMACIÓN DE ELIMINACIÓN
// ========================================

document.addEventListener("DOMContentLoaded", () => {
  let deleteModalData = {
    itemId: null,
    itemName: null,
    itemCode: null,
    callback: null
  };

  // Crear el modal si no existe
  function createDeleteModal() {
    if (document.getElementById('modalEliminar')) return;

    const modalHTML = `
      <div id="modalEliminar" class="superposicion-modal">
        <div class="contenido-modal">
          <div class="encabezado-modal">
            <div class="icono-modal">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2>Confirmar Eliminación</h2>
          </div>
          <div class="cuerpo-modal">
            <p>¿Estás seguro de que deseas eliminar este registro?</p>
            <div class="info-modal">
              <strong>Registro a eliminar:</strong>
              <span id="modalItemName"></span>
            </div>
            <p style="color: #dc2626; font-weight: 600;">
              Esta acción no se puede deshacer.
            </p>
            <div class="grupo-formulario-modal">
              <label for="deleteCode">
                Para confirmar, introduce el código del registro:
                <span style="color: #dc2626;">*</span>
              </label>
              <input 
                type="text" 
                id="deleteCode" 
                placeholder="Ejemplo: #001"
                autocomplete="off"
              />
              <div id="deleteCodeError" class="error-modal">
                El código no coincide. Por favor, verifica e intenta nuevamente.
              </div>
            </div>
          </div>
          <div class="pie-modal">
            <button type="button" class="boton-modal boton-modal-cancelar" id="modalCancelBtn">
              Cancelar
            </button>
            <button type="button" class="boton-modal boton-modal-eliminar" id="modalDeleteBtn">
              <i class="fas fa-trash"></i> Eliminar
            </button>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Event listeners del modal
    const modal = document.getElementById('modalEliminar');
    const cancelBtn = document.getElementById('modalCancelBtn');
    const deleteBtn = document.getElementById('modalDeleteBtn');
    const codeInput = document.getElementById('deleteCode');
    const errorMsg = document.getElementById('deleteCodeError');

    // Cerrar modal
    cancelBtn.addEventListener('click', closeDeleteModal);
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeDeleteModal();
    });

    // Limpiar error al escribir
    codeInput.addEventListener('input', () => {
      codeInput.classList.remove('error');
      errorMsg.classList.remove('activo');
    });

    // Confirmar eliminación
    deleteBtn.addEventListener('click', () => {
      const enteredCode = codeInput.value.trim();
      
      if (enteredCode === deleteModalData.itemCode) {
        // Código correcto
        if (deleteModalData.callback) {
          deleteModalData.callback(deleteModalData.itemId);
        }
        closeDeleteModal();
        
        // Mostrar mensaje de éxito
        showSuccessMessage('Registro eliminado correctamente');
      } else {
        // Código incorrecto
        codeInput.classList.add('error');
        errorMsg.classList.add('activo');
        codeInput.focus();
      }
    });

    // Enter para confirmar
    codeInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        deleteBtn.click();
      }
    });
  }

  // Abrir modal de eliminación
  window.openDeleteModal = function(itemId, itemName, itemCode, callback) {
    createDeleteModal();
    
    deleteModalData = {
      itemId: itemId,
      itemName: itemName,
      itemCode: itemCode,
      callback: callback
    };

    const modal = document.getElementById('modalEliminar');
    const nameElement = document.getElementById('modalItemName');
    const codeInput = document.getElementById('deleteCode');
    const errorMsg = document.getElementById('deleteCodeError');

    nameElement.textContent = itemName;
    codeInput.value = '';
    codeInput.classList.remove('error');
    errorMsg.classList.remove('activo');

    modal.classList.add('activo');
    
    // Focus en el input después de la animación
    setTimeout(() => codeInput.focus(), 300);
  };

  // Cerrar modal
  function closeDeleteModal() {
    const modal = document.getElementById('modalEliminar');
    modal.classList.remove('activo');
    deleteModalData = { itemId: null, itemName: null, itemCode: null, callback: null };
  }

  // Mensaje de éxito
  function showSuccessMessage(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #10b981;
      color: white;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      z-index: 10000;
      animation: slideIn 0.3s ease;
    `;
    notification.innerHTML = `
      <i class="fas fa-check-circle"></i> ${message}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
      notification.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  // Inicializar botones de eliminar en las tablas
  document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Obtener datos de la fila
      const row = this.closest('tr');
      const cells = row.querySelectorAll('td');
      
      // Extraer ID y nombre
      const itemCode = cells[0]?.textContent.trim() || '#000';
      const itemName = cells[1]?.textContent.trim() || 'este registro';
      
      // Abrir modal
      openDeleteModal(
        itemCode,
        itemName,
        itemCode,
        function(id) {
          console.log('Eliminando registro:', id);
          
          // Eliminar la fila de la tabla
          row.remove();
          
          // Aquí puedes agregar la llamada AJAX para eliminar del servidor
          // fetch('/api/delete', { method: 'DELETE', body: JSON.stringify({id}) })
        }
      );
    });
  });
});

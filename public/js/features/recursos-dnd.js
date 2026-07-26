// Arrastrar y soltar archivos/carpetas de Aula Digital sobre otra carpeta
// para moverlos (AJAX). Requiere .recurso-breadcrumbs[data-csrf] en la página.
document.addEventListener('DOMContentLoaded', () => {
    // We expect the container to have a data-csrf attribute
    const container = document.querySelector('.recurso-breadcrumbs');
    if (!container) return;
    const csrfToken = container.dataset.csrf;

    const draggables = document.querySelectorAll('[data-drag-tipo]');
    
    // Areas que pueden recibir un drop: carpetas, y el enlace de "Raíz" o la ruta actual
    const dropzones = document.querySelectorAll('[data-drop-carpeta]');

    draggables.forEach(draggable => {
        draggable.setAttribute('draggable', 'true');
        
        draggable.addEventListener('dragstart', (e) => {
            const type = draggable.dataset.dragTipo;
            const id = draggable.dataset.dragId;
            
            e.dataTransfer.setData('text/plain', JSON.stringify({ type, id }));
            draggable.classList.add('dnd-dragging');
        });

        draggable.addEventListener('dragend', () => {
            draggable.classList.remove('dnd-dragging');
        });
    });

    dropzones.forEach(zone => {
        zone.addEventListener('dragover', (e) => {
            e.preventDefault();
            // Evitar drop sobre sí mismo
            const draggedEl = document.querySelector('.dnd-dragging');
            if (draggedEl === zone) return;
            zone.classList.add('dnd-drag-over');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('dnd-drag-over');
        });

        zone.addEventListener('drop', (e) => {
            e.preventDefault();
            zone.classList.remove('dnd-drag-over');
            
            const draggedEl = document.querySelector('.dnd-dragging');
            if (draggedEl === zone) return;

            try {
                const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                let targetFolderId = 0;

                if (zone.hasAttribute('data-drop-carpeta')) {
                    targetFolderId = zone.dataset.dropCarpeta;
                } else if (zone.dataset.id) {
                    targetFolderId = zone.dataset.id;
                }

                if (data.type === 'carpeta' && data.id == targetFolderId) {
                    return; // Can't drop folder into itself
                }

                // Send AJAX POST
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('tipo', data.type);
                formData.append('idElemento', data.id);
                formData.append('idDestino', targetFolderId);

                fetch('/controladores/profesores/aula/ajax_mover_recurso.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(result => {
                    if (result.ok) {
                        // Reload the page to reflect changes
                        window.location.reload();
                    } else if (window.Toast) {
                        Toast.show(result.msg || 'Error al mover el recurso', 'error');
                    }
                })
                .catch(() => {
                    if (window.Toast) Toast.show('Error de red al mover el recurso', 'error');
                });
            } catch (err) {
                // Payload de drag&drop mal formado (arrastre desde fuera de esta
                // página, por ejemplo) — no hay nada útil que mostrar al usuario.
            }
        });
    });
});

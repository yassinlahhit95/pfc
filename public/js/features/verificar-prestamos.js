// Verificar préstamos activos del estudiante antes de prestar
(function() {
    const selectEstudiante = document.getElementById('idEstudiante');
    const selectDispositivo = document.getElementById('idArticulo');
    const alertaPrestamos = document.getElementById('alerta-prestamos-activos');
    const btnPrestar = document.querySelector('button[name="registrarPrestamo"]');

    if (!selectEstudiante || !alertaPrestamos) return;

    async function verificarPrestamos() {
        const idEstudiante = selectEstudiante.value;
        if (!idEstudiante) {
            alertaPrestamos.innerHTML = '';
            alertaPrestamos.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/api/v1/estudiante-prestamos.php?idEstudiante=${idEstudiante}`);
            const data = await response.json();

            if (!data.ok) {
                alertaPrestamos.innerHTML = '';
                alertaPrestamos.style.display = 'none';
                return;
            }

            if (data.prestamos.length === 0) {
                alertaPrestamos.innerHTML = '';
                alertaPrestamos.style.display = 'none';
                if (btnPrestar) btnPrestar.disabled = false;
                return;
            }

            // Mostrar préstamos activos
            const listaHtml = data.prestamos
                .map(p => `<li><strong>${p.nombreDispositivo}</strong> (desde ${p.fechaPrestamo})</li>`)
                .join('');

            alertaPrestamos.innerHTML = `
                <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 12px; margin: 12px 0;">
                    <strong>⚠️ Atención:</strong> Este estudiante tiene ${data.activos} préstamo(s) activo(s):
                    <ul style="margin: 8px 0 0 20px; padding: 0;">
                        ${listaHtml}
                    </ul>
                    <small style="color: #666;">Asegúrate de que devuelva los dispositivos antes de prestar otro.</small>
                </div>
            `;
            alertaPrestamos.style.display = 'block';

            // Deshabilitar botón si intenta prestar el mismo dispositivo
            if (selectDispositivo && btnPrestar) {
                const dispositivosPrestados = data.prestamos.map(p => p.idDispositivo);
                selectDispositivo.addEventListener('change', () => {
                    const idDispositivo = parseInt(selectDispositivo.value);
                    if (dispositivosPrestados.includes(idDispositivo)) {
                        btnPrestar.disabled = true;
                        btnPrestar.title = 'Este estudiante ya tiene este dispositivo en préstamo';
                        btnPrestar.style.opacity = '0.5';
                    } else {
                        btnPrestar.disabled = false;
                        btnPrestar.title = '';
                        btnPrestar.style.opacity = '1';
                    }
                });
            }
        } catch (err) {
            console.error('Error verificando préstamos:', err);
        }
    }

    // Verificar cuando cambia el estudiante
    selectEstudiante.addEventListener('change', verificarPrestamos);

    // Verificar al cargar si hay estudiante pre-seleccionado
    if (selectEstudiante.value) {
        verificarPrestamos();
    }
})();

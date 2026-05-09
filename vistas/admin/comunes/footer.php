    </main>
</div>

<script>
/**
 * Alterna la visibilidad de la barra lateral en tablets y móviles
 */
function toggleMenu() {
    const sidebar = document.getElementById('barraLateral');
    if (sidebar) {
        sidebar.classList.toggle('activo');
        document.body.classList.toggle('menu-abierto');
    }
}

/**
 * Cierra el menú al hacer clic fuera de él en dispositivos móviles
 */
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('barraLateral');
    const toggle = document.querySelector('.menu-toggle');
    
    // Solo si el menú está abierto y la pantalla es de tamaño tablet/móvil
    if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('activo')) {
        // Si el clic no fue dentro del sidebar ni en el botón de toggle
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('activo');
            document.body.classList.remove('menu-abierto');
        }
    }
});

// Asegurar que el estado se limpie al redimensionar a pantalla grande
window.addEventListener('resize', function() {
    if (window.innerWidth > 992) {
        const sidebar = document.getElementById('barraLateral');
        if (sidebar) {
            sidebar.classList.remove('activo');
            document.body.classList.remove('menu-abierto');
        }
    }
});
</script>

<script src="../../../public/js/filtros.js"></script>
</body>
</html>

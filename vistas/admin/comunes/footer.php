    </main>
</div>

<script>
function toggleMenu() {
    var sidebar = document.getElementById('barraLateral');
    if (sidebar) {
        sidebar.classList.toggle('activo');
        document.body.classList.toggle('menu-abierto');
    }
}

// Cerrar menú al hacer clic fuera (opcional, para móvil)
document.addEventListener('click', function(event) {
    var sidebar = document.getElementById('barraLateral');
    var toggle = document.querySelector('.menu-toggle');
    if (sidebar && sidebar.classList.contains('activo') && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
        sidebar.classList.remove('activo');
        document.body.classList.remove('menu-abierto');
    }
});
</script>

<script src="../../../public/js/filtros.js"></script>
</body>
</html>

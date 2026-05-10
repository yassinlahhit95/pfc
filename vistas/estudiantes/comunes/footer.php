    </main>
</div>

<script>
function toggleMenu() {
    const sidebar = document.getElementById('barraLateral');
    if (sidebar) {
        sidebar.classList.toggle('activo');
        document.body.classList.toggle('menu-abierto');
    }
}

document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('barraLateral');
    const toggle = document.querySelector('.menu-toggle');
    if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('activo')) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('activo');
            document.body.classList.remove('menu-abierto');
        }
    }
});

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
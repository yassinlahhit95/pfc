    </main>
</div>

<script>

const botonMenu = document.getElementById('boton-menu-movil');
const barraLateral = document.querySelector('.barra-lateral');

if (botonMenu) {
    botonMenu.addEventListener('click', () => {
        barraLateral.classList.toggle('mostrar-menu');
    });
}
</script>

<script src="/pfc/public/js/filtros.js"></script>
</body>
</html>


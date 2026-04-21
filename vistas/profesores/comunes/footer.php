    </main>
</div>

<script>
// Toggle Menú Móvil
const botonMenu = document.getElementById('boton-menu-movil');
const barraLateral = document.querySelector('.barra-lateral');

if (botonMenu) {
    botonMenu.addEventListener('click', () => {
        barraLateral.classList.toggle('mostrar-menu');
    });
}
</script>

</body>
</html>
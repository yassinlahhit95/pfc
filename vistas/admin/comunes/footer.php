
</main>
</div>

<script>
// Toggle sidebar en móvil
document.addEventListener('DOMContentLoaded', function() {
  var barraLateral = document.getElementById('barraLateral');
  var overlayBarra = document.getElementById('overlayBarra');
  var botonAbrir = document.getElementById('botonAbrirMenu');
  var botonCerrar = document.getElementById('botonCerrarBarra');

  if (botonAbrir && botonCerrar && barraLateral && overlayBarra) {
    // Abrir sidebar
    botonAbrir.addEventListener('click', function() {
      barraLateral.classList.add('activo');
      overlayBarra.classList.add('activo');
      document.body.style.overflow = 'hidden';
    });

    // Cerrar sidebar
    botonCerrar.addEventListener('click', function() {
      barraLateral.classList.remove('activo');
      overlayBarra.classList.remove('activo');
      document.body.style.overflow = '';
    });

    // Cerrar al hacer clic en overlay
    overlayBarra.addEventListener('click', function() {
      barraLateral.classList.remove('activo');
      overlayBarra.classList.remove('activo');
      document.body.style.overflow = '';
    });
  }
});
</script>

</body>
</html>

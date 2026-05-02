
</main>
</div>

<script>

document.addEventListener('DOMContentLoaded', function() {
  var barraLateral = document.getElementById('barraLateral');
  var overlayBarra = document.getElementById('overlayBarra');
  var botonAbrir = document.getElementById('botonAbrirMenu');
  var botonCerrar = document.getElementById('botonCerrarBarra');

  if (botonAbrir && botonCerrar && barraLateral && overlayBarra) {
    
    botonAbrir.addEventListener('click', function() {
      barraLateral.classList.add('activo');
      overlayBarra.classList.add('activo');
      document.body.style.overflow = 'hidden';
    });

    
    botonCerrar.addEventListener('click', function() {
      barraLateral.classList.remove('activo');
      overlayBarra.classList.remove('activo');
      document.body.style.overflow = '';
    });

    
    overlayBarra.addEventListener('click', function() {
      barraLateral.classList.remove('activo');
      overlayBarra.classList.remove('activo');
      document.body.style.overflow = '';
    });
  }
});
</script>

<script src="../../../public/js/filtros.js"></script>
</body>
</html>





/* ══════════════════════════════════════════════════════════════════════
   LANDING DEL CENTRO — landing.js (vanilla, sin dependencias)
   Menú móvil · contadores · lightbox · formulario de contacto · reveal
   ══════════════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  /* ── Menú móvil ──────────────────────────────────────────────────── */
  var burger = document.getElementById('lp-nav-burger');
  var menuMovil = document.getElementById('lp-nav-movil');
  if (burger && menuMovil) {
    burger.addEventListener('click', function () {
      var abierto = menuMovil.classList.toggle('abierto');
      burger.setAttribute('aria-expanded', abierto ? 'true' : 'false');
      burger.querySelector('i').className = abierto ? 'fas fa-xmark' : 'fas fa-bars';
    });
    menuMovil.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') {
        menuMovil.classList.remove('abierto');
        burger.setAttribute('aria-expanded', 'false');
        burger.querySelector('i').className = 'fas fa-bars';
      }
    });
  }

  /* ── Contadores de cifras ────────────────────────────────────────── */
  var contadores = document.querySelectorAll('[data-contador]');
  if (contadores.length && 'IntersectionObserver' in window) {
    var obsCifras = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
        if (!entrada.isIntersecting) return;
        obsCifras.unobserve(entrada.target);
        var el = entrada.target;
        var destino = parseFloat(String(el.getAttribute('data-contador')).replace(',', '.'));
        if (isNaN(destino)) return; // texto no numérico: se deja tal cual
        var inicio = null;
        var duracion = 1200;
        function paso(ts) {
          if (!inicio) inicio = ts;
          var progreso = Math.min((ts - inicio) / duracion, 1);
          var suavizado = 1 - Math.pow(1 - progreso, 3);
          el.textContent = String(Math.round(destino * suavizado));
          if (progreso < 1) requestAnimationFrame(paso);
        }
        requestAnimationFrame(paso);
      });
    }, { threshold: 0.4 });
    contadores.forEach(function (el) { obsCifras.observe(el); });
  }

  /* ── Lightbox de la galería ──────────────────────────────────────── */
  var itemsGaleria = document.querySelectorAll('[data-lightbox]');
  if (itemsGaleria.length) {
    var lightbox = document.createElement('div');
    lightbox.className = 'lp-lightbox';
    lightbox.innerHTML = '<img src="" alt="">';
    document.body.appendChild(lightbox);
    var imgLightbox = lightbox.querySelector('img');

    itemsGaleria.forEach(function (item) {
      item.addEventListener('click', function () {
        imgLightbox.src = item.getAttribute('data-lightbox');
        lightbox.classList.add('abierto');
      });
    });
    lightbox.addEventListener('click', function () { lightbox.classList.remove('abierto'); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') lightbox.classList.remove('abierto');
    });
  }

  /* ── Formulario de contacto (AJAX) ───────────────────────────────── */
  var form = document.getElementById('lp-form-contacto');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var aviso = document.getElementById('lp-form-aviso');
      var boton = form.querySelector('button[type="submit"]');
      aviso.className = 'lp-form-aviso';
      boton.disabled = true;

      fetch(form.action, { method: 'POST', body: new FormData(form) })
        .then(function (r) { return r.json(); })
        .then(function (datos) {
          aviso.textContent = datos.msg || '';
          aviso.classList.add(datos.ok ? 'ok' : 'error');
          if (datos.ok) form.reset();
        })
        .catch(function () {
          aviso.textContent = 'Error de conexión. Inténtalo de nuevo.';
          aviso.classList.add('error');
        })
        .finally(function () { boton.disabled = false; });
    });
  }

  /* ── Aparición al hacer scroll ───────────────────────────────────── */
  var secciones = document.querySelectorAll('.lp-sec .lp-tarjeta, .lp-sec-cabecera, .lp-galeria-item');
  if (secciones.length && 'IntersectionObserver' in window) {
    var obsAnim = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
        if (entrada.isIntersecting) {
          entrada.target.classList.add('visto');
          obsAnim.unobserve(entrada.target);
        }
      });
    }, { threshold: 0.12 });
    secciones.forEach(function (el) {
      el.classList.add('lp-anim');
      obsAnim.observe(el);
    });
  }
})();

/* ══════════════════════════════════════════════════════════════════════
   LANDING DEL CENTRO — landing.js (vanilla, sin dependencias)
   Módulos: tema · navegación + drawer · scrollspy · volver arriba ·
   contadores · lightbox · formulario de contacto · aparición al hacer scroll
   ══════════════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ── Tema claro / oscuro ─────────────────────────────────────────── */
  (function initTema() {
    var btn = document.getElementById('lp-theme-toggle');
    if (!btn) return;

    function pintarIcono() {
      var oscuro = document.documentElement.getAttribute('data-theme') === 'dark';
      btn.innerHTML = oscuro ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
      btn.setAttribute('aria-pressed', oscuro ? 'true' : 'false');
    }
    pintarIcono();

    btn.addEventListener('click', function () {
      var oscuro = document.documentElement.getAttribute('data-theme') === 'dark';
      if (oscuro) {
        document.documentElement.removeAttribute('data-theme');
      } else {
        document.documentElement.setAttribute('data-theme', 'dark');
      }
      try { localStorage.setItem('theme', oscuro ? 'light' : 'dark'); } catch (e) {}
      pintarIcono();
    });
  }());

  /* ── Navegación: encogido al hacer scroll ────────────────────────── */
  (function initNav() {
    var nav = document.getElementById('lp-nav');
    if (!nav) return;
    var ticking = false;

    function actualizar() {
      nav.classList.toggle('lp-nav-scrolled', window.scrollY > 40);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { requestAnimationFrame(actualizar); ticking = true; }
    }, { passive: true });
    actualizar();
  }());

  /* ── Drawer móvil ────────────────────────────────────────────────── */
  (function initDrawer() {
    var nav      = document.getElementById('lp-nav');
    var burger   = document.getElementById('lp-nav-burger');
    var drawer   = document.getElementById('lp-nav-movil');
    var overlay  = document.getElementById('lp-nav-overlay');
    var closeBtn = document.getElementById('lp-nav-close');
    if (!burger || !drawer || !overlay) return;
    var abierto = false;

    function abrir() {
      abierto = true;
      drawer.classList.add('abierto');
      overlay.classList.add('visible');
      if (nav) nav.classList.add('lp-nav-abierta');
      burger.setAttribute('aria-expanded', 'true');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    }
    function cerrar() {
      abierto = false;
      drawer.classList.remove('abierto');
      overlay.classList.remove('visible');
      if (nav) nav.classList.remove('lp-nav-abierta');
      burger.setAttribute('aria-expanded', 'false');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }

    burger.addEventListener('click', function () { abierto ? cerrar() : abrir(); });
    overlay.addEventListener('click', cerrar);
    if (closeBtn) closeBtn.addEventListener('click', cerrar);
    drawer.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', cerrar); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && abierto) cerrar();
    });
  }());

  /* ── Scrollspy: resalta la sección visible en el menú ────────────── */
  (function initScrollspy() {
    var enlaces = document.querySelectorAll('.lp-nav-links a[data-lp-ancla]');
    if (!enlaces.length || !('IntersectionObserver' in window)) return;

    var mapa = {};
    var secciones = [];
    enlaces.forEach(function (a) {
      var sec = document.getElementById(a.getAttribute('data-lp-ancla'));
      if (sec) { mapa[sec.id] = a; secciones.push(sec); }
    });
    if (!secciones.length) return;

    var visibles = {};
    var obs = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (en) { visibles[en.target.id] = en.isIntersecting; });
      var activa = null;
      secciones.forEach(function (sec) { if (activa === null && visibles[sec.id]) activa = sec.id; });
      enlaces.forEach(function (a) { a.classList.remove('activa'); });
      if (activa && mapa[activa]) mapa[activa].classList.add('activa');
    }, { rootMargin: '-25% 0px -55% 0px' });

    secciones.forEach(function (sec) { obs.observe(sec); });
  }());

  /* ── Botón volver arriba ─────────────────────────────────────────── */
  (function initVolverArriba() {
    var btn = document.getElementById('lp-volver-arriba');
    if (!btn) return;
    var ticking = false;

    function actualizar() {
      btn.classList.toggle('visible', window.scrollY > 600);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { requestAnimationFrame(actualizar); ticking = true; }
    }, { passive: true });

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }());

  /* ── Contadores de cifras ────────────────────────────────────────── */
  (function initContadores() {
    var contadores = document.querySelectorAll('[data-contador]');
    if (!contadores.length || !('IntersectionObserver' in window)) return;

    var obs = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
        if (!entrada.isIntersecting) return;
        obs.unobserve(entrada.target);
        var el = entrada.target;
        var destino = parseFloat(String(el.getAttribute('data-contador')).replace(',', '.'));
        if (isNaN(destino) || reduceMotion) return; // texto no numérico o sin animaciones
        var inicio = null;
        var duracion = 1400;
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
    contadores.forEach(function (el) { obs.observe(el); });
  }());

  /* ── Lightbox de galerías ────────────────────────────────────────── */
  (function initLightbox() {
    var items = document.querySelectorAll('[data-lightbox]');
    if (!items.length) return;

    var lightbox = document.createElement('div');
    lightbox.className = 'lp-lightbox';
    lightbox.innerHTML = '<img src="" alt="">';
    document.body.appendChild(lightbox);
    var img = lightbox.querySelector('img');

    items.forEach(function (item) {
      item.addEventListener('click', function () {
        img.src = item.getAttribute('data-lightbox');
        lightbox.classList.add('abierto');
      });
    });
    lightbox.addEventListener('click', function () { lightbox.classList.remove('abierto'); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') lightbox.classList.remove('abierto');
    });
  }());

  /* ── Formulario de contacto (AJAX) ───────────────────────────────── */
  (function initFormContacto() {
    var form = document.getElementById('lp-form-contacto');
    if (!form) return;

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
  }());

  /* ── Aparición al hacer scroll con cascada ───────────────────────── */
  (function initReveal() {
    if (reduceMotion || !('IntersectionObserver' in window)) return;

    var selector = [
      '.lp-sec .lp-tarjeta', '.lp-sec-cabecera', '.lp-galeria-item',
      '.lp-contacto-grid', '.lp-contacto-solo-datos', '.lp-cifra',
      '.lp-fpdual-lista li', '.lp-blog-card', '.lp-porque-item',
      '.lp-footer-cta-inner', '.lp-testimonio-card', '.lp-ciclo',
      '.lp-empresa', '.lp-faq-item', '.lp-video-pres-content',
      '.lp-video-pres-media', '.lp-cta-sec-inner'
    ].join(', ');

    var elementos = document.querySelectorAll(selector);
    if (!elementos.length) return;

    var obs = new IntersectionObserver(function (entradas) {
      var entrando = entradas.filter(function (e) { return e.isIntersecting; });
      entrando.forEach(function (entrada, i) {
        entrada.target.style.transitionDelay = (Math.min(i, 5) * 0.09) + 's';
        entrada.target.classList.add('visto');
        obs.unobserve(entrada.target);
      });
    }, { rootMargin: '0px 0px -60px 0px', threshold: 0.1 });

    elementos.forEach(function (el) {
      el.classList.add('lp-anim');
      obs.observe(el);
    });
  }());
})();

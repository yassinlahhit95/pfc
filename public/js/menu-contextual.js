(function () {
  'use strict';
  // Menú contextual reutilizable (estilo Aula Digital) para las tablas del panel admin.
  // Usa las clases .recurso-menu-wrap / .recurso-menu-btn / .recurso-menu / .recurso-menu-item
  // que ya están definidas en public/css/aula-digital.css.

  function cerrarMenus() {
    document.querySelectorAll('.recurso-menu.abierto').forEach(function (m) {
      m.classList.remove('abierto');
    });
  }

  function abrirMenu(btn) {
    if (btn._menu === undefined) btn._menu = btn.nextElementSibling;
    var m = btn._menu;
    if (!m || !m.classList.contains('recurso-menu')) return;
    var yaAbierto = m.classList.contains('abierto');
    cerrarMenus();
    if (yaAbierto) return;
    // Se mueve al <body> para que no lo recorte el overflow de la tabla
    if (m.parentNode !== document.body) document.body.appendChild(m);
    m.classList.add('abierto');
    var r = btn.getBoundingClientRect();
    var ancho = m.offsetWidth || 200;
    var izq = r.right - ancho;
    if (izq < 8) izq = 8;
    var top = r.bottom + 6;
    var alto = m.offsetHeight || 180;
    if (top + alto > window.innerHeight) {
      top = r.top - alto - 6;
      if (top < 8) top = 8;
    }
    m.style.top = top + 'px';
    m.style.left = izq + 'px';
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.recurso-menu-btn');
    if (btn) { e.preventDefault(); abrirMenu(btn); return; }
    if (e.target.closest('.recurso-menu-item')) { setTimeout(cerrarMenus, 200); return; }
    if (!e.target.closest('.recurso-menu')) cerrarMenus();
  });
  window.addEventListener('resize', cerrarMenus);
})();

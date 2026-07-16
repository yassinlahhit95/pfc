// Menú contextual reutilizable (estilo Aula Digital) para las tablas del panel admin.
// Usa las clases .recurso-menu-wrap / .recurso-menu-btn / .recurso-menu / .recurso-menu-item
// que ya están definidas en public/css/aula-digital.css.
(function () {
  'use strict';

  function cerrarMenus() {
    document.querySelectorAll('.recurso-menu.abierto').forEach(function (menu) {
      menu.classList.remove('abierto');
    });
  }

  function abrirMenu(btn) {
    if (btn._menu === undefined) btn._menu = btn.nextElementSibling;
    var menu = btn._menu;
    if (!menu || !menu.classList.contains('recurso-menu')) return;
    var yaAbierto = menu.classList.contains('abierto');
    cerrarMenus();
    if (yaAbierto) return;
    // Se mueve al <body> para que no lo recorte el overflow de la tabla
    if (menu.parentNode !== document.body) document.body.appendChild(menu);
    menu.classList.add('abierto');
    var btnRect = btn.getBoundingClientRect();
    var ancho = menu.offsetWidth || 200;
    var izq = btnRect.right - ancho;
    if (izq < 8) izq = 8;
    var top = btnRect.bottom + 6;
    var alto = menu.offsetHeight || 180;
    if (top + alto > window.innerHeight) {
      top = btnRect.top - alto - 6;
      if (top < 8) top = 8;
    }
    menu.style.top = top + 'px';
    menu.style.left = izq + 'px';
  }

  document.addEventListener('click', function (e) {
    // aula-recursos.js manages its own menus; skip to avoid double-open
    if (window.AulaRecursos) return;
    var btn = e.target.closest('.recurso-menu-btn');
    if (btn) { e.preventDefault(); abrirMenu(btn); return; }
    if (e.target.closest('.recurso-menu-item')) { setTimeout(cerrarMenus, 200); return; }
    if (!e.target.closest('.recurso-menu')) cerrarMenus();
  });
  window.addEventListener('resize', cerrarMenus);
})();

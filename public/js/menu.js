// referencias del sidebar y boton hamburguesa
var barra = document.getElementById('barraLateral');
var $barra = $(barra);
var $botonMenu = $('.menu-toggle');

function toggleMenu() {
    if ($barra.hasClass('activo')) {
        $barra.removeClass('activo');
        $('body').removeClass('menu-abierto');
    } else {
        $barra.addClass('activo');
        $('body').addClass('menu-abierto');
    }
}

$(document).on('click', function(e) {
    var ancho = $(window).width();
    if (ancho <= 992 && $barra.hasClass('activo')) {
        var clickSidebar = $(e.target).closest('#barraLateral').length > 0;
        var clickBoton   = $(e.target).closest('.menu-toggle').length > 0;
        if (!clickSidebar && !clickBoton) {
            $barra.removeClass('activo');
            $('body').removeClass('menu-abierto');
        }
    }
});

// cuando se agranda la pantalla, cierra el menu si estaba abierto
$(window).on('resize', function() {
    var w = $(window).width();
    if (w > 992) {
        $barra.removeClass('activo');
        $('body').removeClass('menu-abierto');
    }
});

// ----- Menu flotante de la cabecera (Perfil / Salir) -----
(function () {
    var boton = document.getElementById('sbMore');
    var menu  = document.getElementById('sbMenu');
    if (!boton || !menu) return;

    function cerrar() {
        menu.classList.remove('abierto');
        boton.setAttribute('aria-expanded', 'false');
    }

    function abrir() {
        menu.classList.add('abierto');
        boton.setAttribute('aria-expanded', 'true');
        var r = boton.getBoundingClientRect();
        var ancho = menu.offsetWidth || 190;
        var izq = r.right - ancho;
        if (izq < 8) izq = 8;
        if (izq + ancho > window.innerWidth - 8) izq = window.innerWidth - ancho - 8;
        menu.style.top  = (r.bottom + 8) + 'px';
        menu.style.left = izq + 'px';
    }

    boton.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.contains('abierto') ? cerrar() : abrir();
    });

    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target) && e.target !== boton) cerrar();
    });
    window.addEventListener('resize', cerrar);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrar();
    });
})();

// ----- Secciones plegables del sidebar (acordeón con memoria en el navegador) -----
(function () {
    var secciones = document.querySelectorAll('.barra-lateral .seccion-del-menu');
    if (!secciones.length) return;

    function leer(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
    function guardar(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }

    secciones.forEach(function (sec) {
        var titulo = sec.querySelector('.titulo-de-seccion');
        if (!titulo) return;

        // recoger los enlaces que siguen al título
        var items = [], n = titulo.nextElementSibling;
        while (n) { items.push(n); n = n.nextElementSibling; }
        if (!items.length) return;

        var clave = 'sbSec:' + titulo.textContent.trim();

        // envolver los items para poder animar la altura
        var wrap = document.createElement('div');
        wrap.className = 'seccion-items';
        var inner = document.createElement('div');
        inner.className = 'seccion-items-inner';
        items.forEach(function (it) { inner.appendChild(it); });
        wrap.appendChild(inner);
        sec.appendChild(wrap);

        // chevron a la derecha del título
        var chev = document.createElement('span');
        chev.className = 'seccion-chevron';
        chev.innerHTML = '<svg class="ico" aria-hidden="true"><use href="#ic-chevron"/></svg>';
        titulo.appendChild(chev);
        titulo.classList.add('titulo-clic');
        titulo.setAttribute('role', 'button');
        titulo.setAttribute('tabindex', '0');

        // estado inicial (sin animación) desde el navegador
        var colapsada = leer(clave) === 'c';
        wrap.style.transition = 'none';
        if (colapsada) sec.classList.add('colapsada');
        titulo.setAttribute('aria-expanded', colapsada ? 'false' : 'true');
        void wrap.offsetHeight; // forzar reflow
        requestAnimationFrame(function () { wrap.style.transition = ''; });

        function alternar() {
            var ahora = sec.classList.toggle('colapsada');
            titulo.setAttribute('aria-expanded', ahora ? 'false' : 'true');
            guardar(clave, ahora ? 'c' : 'o');
        }
        titulo.addEventListener('click', alternar);
        titulo.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); alternar(); }
        });
    });
})();

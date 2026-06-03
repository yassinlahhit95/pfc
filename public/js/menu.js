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

// ----- Secciones del sidebar: plegar/desplegar + reordenar (con memoria en el navegador) -----
(function () {
    var nav = document.querySelector('.barra-lateral .menu-navegacion');
    if (!nav) return;
    var secciones = Array.prototype.slice.call(nav.querySelectorAll('.seccion-del-menu'));
    if (!secciones.length) return;
    var sep = nav.querySelector('.separador-menu-inferior');
    var ORDEN_KEY = 'sbSecOrder';
    var arrastrada = null;

    function leer(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
    function guardar(k, v) { try { localStorage.setItem(k, v); } catch (e) {} }

    function guardarOrden() {
        var orden = Array.prototype.slice.call(nav.querySelectorAll('.seccion-del-menu'))
            .map(function (s) { return s.dataset.sec; });
        guardar(ORDEN_KEY, JSON.stringify(orden));
    }

    secciones.forEach(function (sec) {
        var titulo = sec.querySelector('.titulo-de-seccion');
        if (!titulo) return;

        // envolver los items para animar la altura
        var items = [], n = titulo.nextElementSibling;
        while (n) { items.push(n); n = n.nextElementSibling; }
        var wrap = document.createElement('div');
        wrap.className = 'seccion-items';
        var inner = document.createElement('div');
        inner.className = 'seccion-items-inner';
        items.forEach(function (it) { inner.appendChild(it); });
        wrap.appendChild(inner);
        sec.appendChild(wrap);

        // reconstruir el título: grip (arrastrar) + etiqueta + botón abrir/cerrar
        var txt = titulo.textContent.trim();
        sec.dataset.sec = txt;
        titulo.textContent = '';
        titulo.classList.add('titulo-clic');
        titulo.setAttribute('draggable', 'true');

        var grip = document.createElement('span');
        grip.className = 'seccion-grip';
        grip.innerHTML = '<svg class="ico" aria-hidden="true"><use href="#ic-grip"/></svg>';

        var label = document.createElement('span');
        label.className = 'seccion-label';
        label.textContent = txt;

        var toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.className = 'seccion-toggle';
        toggle.setAttribute('aria-label', 'Abrir o cerrar sección');
        toggle.innerHTML = '<svg class="ico ico-menos" aria-hidden="true"><use href="#ic-menos"/></svg>' +
                           '<svg class="ico ico-mas" aria-hidden="true"><use href="#ic-mas"/></svg>';

        titulo.appendChild(grip);
        titulo.appendChild(label);
        titulo.appendChild(toggle);

        // estado colapsado inicial (sin animación)
        var ckey = 'sbSec:' + txt;
        var colapsada = leer(ckey) === 'c';
        wrap.style.transition = 'none';
        if (colapsada) sec.classList.add('colapsada');
        toggle.setAttribute('aria-expanded', colapsada ? 'false' : 'true');
        void wrap.offsetHeight;
        requestAnimationFrame(function () { wrap.style.transition = ''; });

        // abrir / cerrar: botón derecho Y clic en el título
        function alternar() {
            var ahora = sec.classList.toggle('colapsada');
            toggle.setAttribute('aria-expanded', ahora ? 'false' : 'true');
            guardar(ckey, ahora ? 'c' : 'o');
        }
        toggle.addEventListener('click', function (e) { e.stopPropagation(); alternar(); });
        titulo.addEventListener('click', function (e) {
            if (e.target.closest('.seccion-toggle')) return;
            alternar();
        });

        // arrastrar para reordenar (desde el título, no desde el botón)
        titulo.addEventListener('dragstart', function (e) {
            if (e.target.closest('.seccion-toggle')) { e.preventDefault(); return; }
            arrastrada = sec;
            sec.classList.add('arrastrando');
            try { e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', txt); } catch (_) {}
        });
        titulo.addEventListener('dragend', function () {
            sec.classList.remove('arrastrando');
            arrastrada = null;
            guardarOrden();
        });
    });

    // zona de soltado: todo el nav (evita el cursor de "prohibido")
    nav.addEventListener('dragover', function (e) {
        if (!arrastrada) return;
        e.preventDefault();
        try { e.dataTransfer.dropEffect = 'move'; } catch (_) {}
        var otras = Array.prototype.slice.call(nav.querySelectorAll('.seccion-del-menu'))
            .filter(function (s) { return s !== arrastrada; });
        var ref = null;
        for (var i = 0; i < otras.length; i++) {
            var r = otras[i].getBoundingClientRect();
            if (e.clientY < r.top + r.height / 2) { ref = otras[i]; break; }
        }
        nav.insertBefore(arrastrada, ref || sep);
    });
    nav.addEventListener('drop', function (e) { if (arrastrada) e.preventDefault(); });

    // aplicar el orden guardado al cargar
    var raw = leer(ORDEN_KEY);
    if (raw) {
        var orden;
        try { orden = JSON.parse(raw); } catch (e) { orden = null; }
        if (Array.isArray(orden)) {
            var mapa = {};
            nav.querySelectorAll('.seccion-del-menu').forEach(function (s) { mapa[s.dataset.sec] = s; });
            orden.forEach(function (nombre) {
                if (mapa[nombre]) nav.insertBefore(mapa[nombre], sep);
            });
        }
    }
})();

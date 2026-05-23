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

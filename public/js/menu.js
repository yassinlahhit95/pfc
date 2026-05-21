var $sidebar = $('#barraLateral');
var $botonMenu = $('.menu-toggle');

function toggleMenu() {
    if ($sidebar.hasClass('activo')) {
        $sidebar.removeClass('activo');
        $('body').removeClass('menu-abierto');
    } else {
        $sidebar.addClass('activo');
        $('body').addClass('menu-abierto');
    }
}

$(document).on('click', function(e) {
    if ($(window).width() <= 992 && $sidebar.hasClass('activo')) {
        var enSidebar = $(e.target).closest('#barraLateral').length > 0;
        var enBoton = $(e.target).closest('.menu-toggle').length > 0;
        if (!enSidebar && !enBoton) {
            $sidebar.removeClass('activo');
            $('body').removeClass('menu-abierto');
        }
    }
});

$(window).on('resize', function() {
    if ($(window).width() > 992) {
        $sidebar.removeClass('activo');
        $('body').removeClass('menu-abierto');
    }
});

var sidebar = $('#barraLateral');
var toggle = $('.menu-toggle');

function toggleMenu() {
    sidebar.toggleClass('activo');
    $('body').toggleClass('menu-abierto');
}

$(document).on('click', function(e) {
    if ($(window).width() <= 992 && sidebar.hasClass('activo')) {
        var clickEnSidebar = sidebar.is(e.target) || sidebar.has(e.target).length > 0;
        var clickEnToggle  = toggle.is(e.target)  || toggle.has(e.target).length > 0;
        if (!clickEnSidebar && !clickEnToggle) {
            sidebar.removeClass('activo');
            $('body').removeClass('menu-abierto');
        }
    }
});

$(window).on('resize', function() {
    if ($(window).width() > 992) {
        sidebar.removeClass('activo');
        $('body').removeClass('menu-abierto');
    }
});

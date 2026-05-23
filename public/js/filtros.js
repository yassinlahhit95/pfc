function filtrarTabla(inputId, tablaId) {
    var q = $('#' + inputId).val().toLowerCase();
    console.log('filtrando tabla:', tablaId, 'con:', q);

    $('#' + tablaId + ' tbody tr').each(function() {
        let s = $(this).text().toLowerCase();
        if (s.indexOf(q) !== -1) {
            $(this).removeClass('fila-filtro-oculta');
        } else {
            $(this).addClass('fila-filtro-oculta');
        }
    });

    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

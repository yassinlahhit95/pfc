function filtrarTabla(inputId, tablaId) {
    var texto = $('#' + inputId).val().toLowerCase();

    $('#' + tablaId + ' tbody tr').each(function() {
        var textoFila = $(this).text().toLowerCase();
        if (textoFila.indexOf(texto) !== -1) {
            $(this).removeClass('fila-filtro-oculta');
        } else {
            $(this).addClass('fila-filtro-oculta');
        }
    });

    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

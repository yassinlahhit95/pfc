function filtrarTabla(inputId, tablaId) {
    var texto = $('#' + inputId).val().toLowerCase();
    $('#' + tablaId + ' tbody tr').each(function() {
        var coincide = $(this).text().toLowerCase().includes(texto);
        $(this).toggleClass('fila-filtro-oculta', !coincide);
    });

    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

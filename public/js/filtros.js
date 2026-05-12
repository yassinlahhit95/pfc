// Filtra las filas de una tabla según el texto del select o input
function filtrarTabla(inputId, tablaId) {
    var texto = $('#' + inputId).val().toLowerCase();
    $('#' + tablaId + ' tbody tr').each(function() {
        var coincide = $(this).text().toLowerCase().includes(texto);
        $(this).toggleClass('fila-filtro-oculta', !coincide);
    });

    // Si hay paginación activa en esta tabla, volvemos a la página 1
    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

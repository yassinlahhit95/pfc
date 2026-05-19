// Función genérica para filtrar filas de una tabla mientras el usuario escribe.
function filtrarTabla(inputId, tablaId) {
    // Pillamos el texto del input y lo pasamos a minúsculas para que la búsqueda no sea sensible.
    var texto = $('#' + inputId).val().toLowerCase();

    // Recorremos todas las filas del cuerpo de la tabla
    $('#' + tablaId + ' tbody tr').each(function() {
        // Miramos si el texto de la fila incluye lo que buscamos
        var coincide = $(this).text().toLowerCase().includes(texto);
        
        // Si no coincide, le metemos la clase para ocultarla (está en el CSS)
        $(this).toggleClass('fila-filtro-oculta', !coincide);
    });

    // OJO: Si la tabla tiene paginación, hay que resetearla al filtrar
    // porque si no se queda en una página que igual ya no tiene resultados.
    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

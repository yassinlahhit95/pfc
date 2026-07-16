// Filtros de texto para tablas: filtrarTabla() busca en 1 input,
// filtrarTablaMulti() combina varios inputs con data-filtro-tabla/data-filtro-campo.
// Ambos re-sincronizan la paginación (paginacion.js) tras filtrar.

function filtrarTabla(inputId, tablaId) {
    var q = $('#' + inputId).val().toLowerCase();

    $('#' + tablaId + ' tbody tr').each(function() {
        let texto = $(this).text().toLowerCase();
        if (texto.indexOf(q) !== -1) {
            $(this).removeClass('fila-filtro-oculta');
        } else {
            $(this).addClass('fila-filtro-oculta');
        }
    });

    if (typeof resetearPaginacion === 'function' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

function filtrarTablaMulti(tablaId) {
    var filtros = {};
    $('[data-filtro-tabla="' + tablaId + '"]').each(function() {
        filtros[$(this).data('filtro-campo')] = $(this).val().toLowerCase();
    });

    $('#' + tablaId + ' tbody tr').each(function() {
        var $tr = $(this);
        var visible = true;
        $.each(filtros, function(campo, valor) {
            if (!valor) return true;
            if ($tr.find('[data-campo="' + campo + '"]').text().toLowerCase().indexOf(valor) === -1) {
                visible = false;
                return false;
            }
        });
        $tr.toggleClass('fila-filtro-oculta', !visible);
    });

    if (typeof resetearPaginacion === 'function' && typeof _paginaciones !== 'undefined' && _paginaciones && _paginaciones[tablaId]) {
        resetearPaginacion(tablaId);
    }
}

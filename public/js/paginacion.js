var _paginaciones = {};

function iniciarPaginacion(tablaId, filasPorPagina) {
    if ($('#' + tablaId).length === 0) return;
    console.log('iniciando paginacion en:', tablaId, 'filas por pagina:', filasPorPagina);

    _paginaciones[tablaId] = {
        filasPorPagina: filasPorPagina,
        paginaActual: 1
    };

    _mostrarPaginaTabla(tablaId, 1);
}

function _mostrarPaginaTabla(tablaId, pagina) {
    var config = _paginaciones[tablaId];
    if (!config) return;

    let filas = $('#' + tablaId + ' tbody tr').toArray();
    let visibles = filas.filter(function(tr) {
        return !$(tr).hasClass('fila-filtro-oculta');
    });

    var total = Math.max(1, Math.ceil(visibles.length / config.filasPorPagina));
    if (pagina < 1) pagina = 1;
    if (pagina > total) pagina = total;
    config.paginaActual = pagina;

    var inicio = (pagina - 1) * config.filasPorPagina;
    var fin = inicio + config.filasPorPagina;

    $.each(visibles, function(idx, tr) {
        if (idx >= inicio && idx < fin) {
            $(tr).show();
        } else {
            $(tr).hide();
        }
    });

    _renderControles(tablaId, pagina, total);
}

function _renderControles(tablaId, pagina, total) {
    var ctId = 'paginacion-' + tablaId;
    var $contenedor = $('#' + ctId);

    if ($contenedor.length === 0) {
        $contenedor = $('<div>').attr('id', ctId).addClass('paginacion-controles');
        $('#' + tablaId).parent().after($contenedor);
    }

    if (total <= 1) {
        $contenedor.html('');
        return;
    }

    let html = '';
    html += '<button class="btn-pagina" onclick="irAPagina(\'' + tablaId + '\',' + (pagina - 1) + ')"';
    if (pagina === 1) html += ' disabled';
    html += '>&#8592; Anterior</button>';

    var txt = 'Página ' + pagina + ' de ' + total;
    html += '<span class="info-pagina">' + txt + '</span>';

    html += '<button class="btn-pagina" onclick="irAPagina(\'' + tablaId + '\',' + (pagina + 1) + ')"';
    if (pagina === total) html += ' disabled';
    html += '>Siguiente &#8594;</button>';

    $contenedor.html(html);
}

function irAPagina(tablaId, pagina) {
    console.log('ir a pagina:', pagina, 'en tabla:', tablaId);
    _mostrarPaginaTabla(tablaId, pagina);
}

function resetearPaginacion(tablaId) {
    _mostrarPaginaTabla(tablaId, 1);
}

var _paginaciones = {};

function iniciarPaginacion(tablaId, filasPorPagina) {
    var tabla = document.getElementById(tablaId);
    if (!tabla) return;

    _paginaciones[tablaId] = {
        tabla: tabla,
        filasPorPagina: filasPorPagina,
        paginaActual: 1
    };

    _mostrarPaginaTabla(tablaId, 1);
}

function _mostrarPaginaTabla(tablaId, pagina) {
    var config = _paginaciones[tablaId];
    if (!config) return;

    var filas = Array.from(config.tabla.querySelectorAll('tbody tr'));

    var visibles = filas.filter(function(tr) {
        return !tr.classList.contains('fila-filtro-oculta');
    });

    var total = Math.max(1, Math.ceil(visibles.length / config.filasPorPagina));

    if (pagina < 1) pagina = 1;
    if (pagina > total) pagina = total;
    config.paginaActual = pagina;

    var inicio = (pagina - 1) * config.filasPorPagina;
    var fin = inicio + config.filasPorPagina;

    visibles.forEach(function(tr, idx) {
        tr.style.display = (idx >= inicio && idx < fin) ? '' : 'none';
    });

    _renderControles(tablaId, pagina, total);
}

function _renderControles(tablaId, pagina, total) {
    var ctId = 'paginacion-' + tablaId;
    var contenedor = document.getElementById(ctId);

    if (!contenedor) {
        var config = _paginaciones[tablaId];
        contenedor = document.createElement('div');
        contenedor.id = ctId;
        contenedor.className = 'paginacion-controles';
        config.tabla.parentNode.insertAdjacentElement('afterend', contenedor);
    }

    if (total <= 1) {
        contenedor.innerHTML = '';
        return;
    }

    var html = '';
    html += '<button class="btn-pagina" onclick="irAPagina(\'' + tablaId + '\',' + (pagina - 1) + ')"';
    if (pagina === 1) html += ' disabled';
    html += '>&#8592; Anterior</button>';

    html += '<span class="info-pagina">Página ' + pagina + ' de ' + total + '</span>';

    html += '<button class="btn-pagina" onclick="irAPagina(\'' + tablaId + '\',' + (pagina + 1) + ')"';
    if (pagina === total) html += ' disabled';
    html += '>Siguiente &#8594;</button>';

    contenedor.innerHTML = html;
}

function irAPagina(tablaId, pagina) {
    _mostrarPaginaTabla(tablaId, pagina);
}

function resetearPaginacion(tablaId) {
    _mostrarPaginaTabla(tablaId, 1);
}

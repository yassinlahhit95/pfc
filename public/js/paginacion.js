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

    var todasLasFilas = Array.from(config.tabla.querySelectorAll('tbody tr'));

    var filasActivas = todasLasFilas.filter(function(tr) {
        return !tr.classList.contains('fila-filtro-oculta');
    });

    var totalPaginas = Math.max(1, Math.ceil(filasActivas.length / config.filasPorPagina));

    if (pagina < 1) pagina = 1;
    if (pagina > totalPaginas) pagina = totalPaginas;
    config.paginaActual = pagina;

    var inicio = (pagina - 1) * config.filasPorPagina;
    var fin = inicio + config.filasPorPagina;

    filasActivas.forEach(function(tr, idx) {
        tr.style.display = (idx >= inicio && idx < fin) ? '' : 'none';
    });

    _renderControles(tablaId, pagina, totalPaginas);
}

function _renderControles(tablaId, pagina, totalPaginas) {
    var contenedorId = 'paginacion-' + tablaId;
    var contenedor = document.getElementById(contenedorId);

    if (!contenedor) {
        var config = _paginaciones[tablaId];
        contenedor = document.createElement('div');
        contenedor.id = contenedorId;
        contenedor.className = 'paginacion-controles';
        config.tabla.parentNode.insertAdjacentElement('afterend', contenedor);
    }

    if (totalPaginas <= 1) {
        contenedor.innerHTML = '';
        return;
    }

    var html = '';
    html += '<button class="btn-pagina" onclick="irAPagina(\'' + tablaId + '\',' + (pagina - 1) + ')"';
    if (pagina === 1) html += ' disabled';
    html += '>&#8592; Anterior</button>';

    html += '<span class="info-pagina">Página ' + pagina + ' de ' + totalPaginas + '</span>';

    html += '<button class="btn-pagina" onclick="irAPagina(\'' + tablaId + '\',' + (pagina + 1) + ')"';
    if (pagina === totalPaginas) html += ' disabled';
    html += '>Siguiente &#8594;</button>';

    contenedor.innerHTML = html;
}

function irAPagina(tablaId, pagina) {
    _mostrarPaginaTabla(tablaId, pagina);
}

function resetearPaginacion(tablaId) {
    _mostrarPaginaTabla(tablaId, 1);
}

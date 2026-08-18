// Paginación client-side para tablas .tabla-datos: oculta filas con la clase
// .pag-oculta y pinta los controles debajo. iniciarPaginacion(tablaId, filasPorPagina)
// arranca; resetearPaginacion(tablaId) se llama tras filtrar (ver filtros.js).
var _paginaciones = {};

(function() {
    var styleEl = document.createElement('style');
    styleEl.textContent = 'tr.pag-oculta{display:none}';
    document.head.appendChild(styleEl);
})();

function iniciarPaginacion(tablaId, filasPorPagina) {
    if ($('#' + tablaId).length === 0) return;
    _paginaciones[tablaId] = { filasPorPagina: filasPorPagina, paginaActual: 1 };
    _mostrarPaginaTabla(tablaId, 1);
}

function _mostrarPaginaTabla(tablaId, pagina) {
    var config = _paginaciones[tablaId];
    if (!config) return;

    var filas = $('#' + tablaId + ' tbody tr').toArray();
    var visibles = filas.filter(function(tr) {
        return !$(tr).hasClass('fila-filtro-oculta');
    });

    var totalFilas = visibles.length;
    var total = Math.max(1, Math.ceil(totalFilas / config.filasPorPagina));
    if (pagina < 1) pagina = 1;
    if (pagina > total) pagina = total;
    config.paginaActual = pagina;

    var inicio = (pagina - 1) * config.filasPorPagina;
    var fin = inicio + config.filasPorPagina;

    $.each(filas, function(_, tr) { tr.classList.add('pag-oculta'); });
    $.each(visibles, function(idx, tr) {
        if (idx >= inicio && idx < fin) tr.classList.remove('pag-oculta');
    });

    _renderControles(tablaId, pagina, total, totalFilas, inicio, fin);
}

function _renderControles(tablaId, pagina, total, totalFilas, inicio, fin) {
    var ctId = 'paginacion-' + tablaId;
    var $ct = $('#' + ctId);
    if ($ct.length === 0) {
        $ct = $('<div>').attr('id', ctId).addClass('paginacion-wrap');
        $('#' + tablaId).parent().after($ct);
    }

    if (totalFilas === 0) { $ct.html(''); return; }

    var desde = inicio + 1;
    var hasta = Math.min(fin, totalFilas);
    var info = '<span class="pag-info">Mostrando <b>' + desde + '–' + hasta + '</b> de <b>' + totalFilas + '</b> entradas</span>';

    if (total <= 1) { $ct.html(info); return; }

    var pg = '<div class="pag-pages">';
    pg += '<button type="button" class="pag-btn pag-nav" onclick="irAPagina(\'' + tablaId + '\',' + (pagina - 1) + ')"' + (pagina === 1 ? ' disabled' : '') + ' aria-label="Página anterior"><i class="fas fa-chevron-left"></i></button>';

    var nums = _buildPageNumbers(pagina, total);
    $.each(nums, function(_, pageNum) {
        if (pageNum === '...') {
            pg += '<span class="pag-ellipsis">…</span>';
        } else {
            pg += '<button type="button" class="pag-btn' + (pageNum === pagina ? ' activo' : '') + '" onclick="irAPagina(\'' + tablaId + '\',' + pageNum + ')">' + pageNum + '</button>';
        }
    });

    pg += '<button type="button" class="pag-btn pag-nav" onclick="irAPagina(\'' + tablaId + '\',' + (pagina + 1) + ')"' + (pagina === total ? ' disabled' : '') + ' aria-label="Página siguiente"><i class="fas fa-chevron-right"></i></button>';
    pg += '</div>';

    $ct.html(info + pg);
}

function _buildPageNumbers(current, total) {
    if (total <= 7) {
        var arr = [];
        for (var i = 1; i <= total; i++) arr.push(i);
        return arr;
    }
    var pages = [1];
    var start = Math.max(2, current - 1);
    var end = Math.min(total - 1, current + 1);
    if (start > 2) pages.push('...');
    for (var i = start; i <= end; i++) pages.push(i);
    if (end < total - 1) pages.push('...');
    pages.push(total);
    return pages;
}

function irAPagina(tablaId, pagina) {
    _mostrarPaginaTabla(tablaId, pagina);
}

function resetearPaginacion(tablaId) {
    _mostrarPaginaTabla(tablaId, 1);
}

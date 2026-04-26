/**
 * Filtros dinámicos para tablas usando jQuery
 * CPS IBAIONDO
 */

function filtrarTabla(inputId, tablaId) {
    // Obtenemos el texto del input en mayúsculas
    var texto = $("#" + inputId).val().toLowerCase();

    // Filtramos las filas de la tabla
    $("#" + tablaId + " tbody tr").filter(function() {
        // Mostramos si coincide, ocultamos si no
        $(this).toggle($(this).text().toLowerCase().indexOf(texto) > -1);
    });
}

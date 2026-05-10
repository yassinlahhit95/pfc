// Filtra las filas de una tabla según el texto del input
function filtrarTabla(inputId, tablaId) {
    var texto = $('#' + inputId).val().toLowerCase();
    $('#' + tablaId + ' tbody tr').each(function() {
        $(this).toggle($(this).text().toLowerCase().includes(texto));
    });
}

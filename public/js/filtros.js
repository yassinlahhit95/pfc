function filtrarTabla(inputId, tablaId) {
    var texto = $("#" + inputId).val().toLowerCase();
    $("#" + tablaId + " tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(texto) > -1);
    });
}
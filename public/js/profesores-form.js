$(document).ready(function() {

    function actualizarModulos() {
        var haySeleccionado = false;

        $('.check-ciclo').each(function() {
            var grupo = $('#grupo-ciclo-' + $(this).val());
            grupo.toggleClass('oculto', !this.checked);
            if (this.checked) haySeleccionado = true;
        });

        $('#msg-seleccionar-ciclo').toggleClass('oculto', haySeleccionado);
    }

    $('.check-ciclo').on('change', actualizarModulos);

    actualizarModulos();
});

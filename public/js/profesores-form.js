$(document).ready(function() {

    // Muestra u oculta los módulos según el ciclo seleccionado
    function actualizarModulos() {
        var haySeleccionado = false;

        $('.check-ciclo').each(function() {
            var grupo = $('.grupo-modulos[data-ciclo-id="' + $(this).data('id') + '"]');
            grupo.toggleClass('d-none', !this.checked);
            if (this.checked) haySeleccionado = true;
        });

        // Muestra el mensaje si no hay ningún ciclo seleccionado
        $('#msg-seleccionar-ciclo').toggleClass('d-none', haySeleccionado);
    }

    $('.check-ciclo').on('change', actualizarModulos);

    actualizarModulos();
});

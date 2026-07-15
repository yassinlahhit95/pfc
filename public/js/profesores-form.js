// Formulario de profesor: muestra solo los módulos de los ciclos marcados
// con los checkboxes .check-ciclo; avisa si no hay ninguno seleccionado.
$(document).ready(function() {

    function actualizarModulos() {
        var hayAlguno = false;

        $('.check-ciclo').each(function() {
            var $grupo = $('#grupo-ciclo-' + $(this).val());
            if (this.checked) {
                $grupo.removeClass('oculto');
                hayAlguno = true;
            } else {
                $grupo.addClass('oculto');
            }
        });

        if (hayAlguno) {
            $('#msg-seleccionar-ciclo').addClass('oculto');
        } else {
            $('#msg-seleccionar-ciclo').removeClass('oculto');
        }
    }

    $('.check-ciclo').on('change', actualizarModulos);

    actualizarModulos();
});

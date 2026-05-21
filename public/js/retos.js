$(document).ready(function() {
    var $form = $('form.formulario');
    if ($form.length === 0) return;

    $form.on('submit', function(e) {
        var inicio = $('input[name="fechaInicioReto"]').val() || $('input[name="fechaInicio"]').val();
        var fin = $('input[name="fechaFinReto"]').val() || $('input[name="fechaFin"]').val();
        var horas = parseInt($('input[name="horasReto"]').val());

        if (inicio && fin && fin < inicio) {
            e.preventDefault();
            alert('La fecha de fin no puede ser anterior a la de inicio.');
            return;
        }

        if (inicio && fin && !isNaN(horas)) {
            var fechaInicio = new Date(inicio);
            var fechaFin = new Date(fin);
            var diasLaborables = 0;
            var diaActual = new Date(fechaInicio);

            while (diaActual <= fechaFin) {
                var diaSemana = diaActual.getDay();
                if (diaSemana != 0 && diaSemana != 6) {
                    diasLaborables++;
                }
                diaActual.setDate(diaActual.getDate() + 1);
            }

            var maxHoras = diasLaborables * 6;
            if (horas > maxHoras) {
                e.preventDefault();
                alert('Las horas (' + horas + 'h) superan el limite de ' + maxHoras + 'h (' + diasLaborables + ' dias x 6h).');
            }
        }
    });
});

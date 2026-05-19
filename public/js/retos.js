$(document).ready(function() {
    var form = $('#formReto').length ? $('#formReto') : $('form.formulario');
    if (!form.length) return;

    form.on('submit', function(e) {
        var inicio = $('#fechaInicioReto').val() || $('input[name="fechaInicioReto"]').val() || $('input[name="fechaInicio"]').val();
        var fin = $('#fechaFinReto').val() || $('input[name="fechaFinReto"]').val() || $('input[name="fechaFin"]').val();
        var horas = parseInt($('#horasReto').val() || $('input[name="horasReto"]').val());
        
        if (inicio && fin && fin < inicio) {
            e.preventDefault();
            alert('La fecha de fin no puede ser anterior a la de inicio.');
            return;
        }

        if (inicio && fin && !isNaN(horas)) {
            var start = new Date(inicio);
            var end = new Date(fin);
            var workingDays = 0;
            var current = new Date(start);

            while (current <= end) {
                var day = current.getDay();
                if (day !== 0 && day !== 6) {
                    workingDays++;
                }
                current.setDate(current.getDate() + 1);
            }

            var maxAllowed = workingDays * 6;
            if (horas > maxAllowed) {
                e.preventDefault();
                alert('Las horas estimadas (' + horas + 'h) superan el máximo de ' + maxAllowed + 'h para el periodo seleccionado (' + workingDays + ' días laborables x 6h/día).');
                return;
            }
        }
    });
});

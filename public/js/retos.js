$(document).ready(function() {
    var form = $('#formReto');
    if (!form.length) return;

    form.on('submit', function(e) {
        var inicio = $('#fechaInicioReto').val();
        var fin = $('#fechaFinReto').val();
        var horas = parseInt($('#horasReto').val());

        if (!inicio || !fin || !horas) return;

        var fechaInicio = new Date(inicio);
        var fechaFin = new Date(fin);

        if (fechaInicio > fechaFin) {
            e.preventDefault();
            alert('La fecha de inicio no puede ser posterior a la fecha de fin.');
            return;
        }

        var dias = 0;
        var actual = new Date(fechaInicio);
        while (actual <= fechaFin) {
            var diaSemana = actual.getDay();
            if (diaSemana !== 0 && diaSemana !== 6) dias++;
            actual.setDate(actual.getDate() + 1);
        }

        var maxHoras = dias * 6;
        if (horas > maxHoras) {
            e.preventDefault();
            alert('Las horas estimadas (' + horas + ') superan el máximo para estas fechas (' + maxHoras + ' horas, 6h/día de lunes a viernes).');
        }
    });
});

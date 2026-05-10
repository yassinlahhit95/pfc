$(document).ready(function() {
    var form = $('#formReto');
    if (!form.length) return;

    // Valida las fechas y las horas al enviar el formulario
    form.on('submit', function(e) {
        var inicio = $('#fechaInicioReto').val();
        var fin = $('#fechaFinReto').val();
        var horas = parseInt($('#horasReto').val());

        if (!inicio || !fin || !horas) return;

        var fechaInicio = new Date(inicio);
        var fechaFin = new Date(fin);

        // La fecha de inicio no puede ser posterior a la de fin
        if (fechaInicio > fechaFin) {
            e.preventDefault();
            alert('La fecha de inicio no puede ser posterior a la fecha de fin.');
            return;
        }

        // Cuenta los días laborables (lunes a viernes) entre las dos fechas
        var dias = 0;
        var actual = new Date(fechaInicio);
        while (actual <= fechaFin) {
            var diaSemana = actual.getDay();
            if (diaSemana !== 0 && diaSemana !== 6) dias++;
            actual.setDate(actual.getDate() + 1);
        }

        // Máximo 6 horas por día laborable
        var maxHoras = dias * 6;
        if (horas > maxHoras) {
            e.preventDefault();
            alert('Las horas estimadas (' + horas + ') superan el máximo para estas fechas (' + maxHoras + ' horas, 6h/día de lunes a viernes).');
        }
    });
});

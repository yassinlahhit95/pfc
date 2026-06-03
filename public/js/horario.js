/**
 * Cuadro Horario - interacciones del director (CRUD por AJAX).
 * Todo encapsulado en el objeto Horario para no contaminar el ambito global.
 * Depende de jQuery (ya cargado en el nav) y de window.HORARIO_AULAS.
 */
var Horario = (function () {
    var $app, idCiclo, csrf;
    var seleccionada = null; // tarjeta seleccionada por clic

    function escapar(texto) {
        return $('<div>').text(texto == null ? '' : texto).html();
    }

    function rutaControlador(archivo) {
        // La vista vive en vistas/admin/horario/ -> 3 niveles hasta la raiz
        return '../../../controladores/admin/horario/' + archivo;
    }

    function construirSelectAula(idSeleccionada) {
        var aulas = window.HORARIO_AULAS || [];
        var opts = '<option value="">— Aula —</option>';
        aulas.forEach(function (a) {
            var sel = (String(a.id) === String(idSeleccionada)) ? ' selected' : '';
            opts += '<option value="' + a.id + '"' + sel + '>Aula ' + escapar(a.codigo) + '</option>';
        });
        return '<select class="horario-aula-select" aria-label="Aula">' + opts + '</select>';
    }

    function pintarAsignada($celda, datos, idAula) {
        var html = '' +
            '<div class="horario-asignado" style="background-color:' + escapar(datos.color) + ';"' +
            ' data-modulo="' + escapar(datos.modulo) + '" data-profesor="' + escapar(datos.profesor) + '">' +
                '<button type="button" class="horario-limpiar" aria-label="Quitar"><i class="fas fa-xmark"></i></button>' +
                '<div class="horario-asignado-modulo">' + escapar(datos.moduloNombre) + '</div>' +
                '<div class="horario-asignado-prof">' + escapar(datos.profesorNombre) + '</div>' +
                construirSelectAula(idAula) +
            '</div>';
        $celda.html(html);
    }

    function pintarVacia($celda) {
        $celda.html('<div class="horario-vacia"><i class="fas fa-plus"></i> Asignar</div>');
    }

    // Guarda modulo + profesor (+ aula si la celda ya tenia una) al colocar una tarjeta
    function asignar($celda, datos) {
        var idAula = $celda.find('.horario-aula-select').val() || '';
        $.post(rutaControlador('guardar.php'), {
            csrf_token: csrf,
            idCiclo: idCiclo,
            dia: $celda.data('dia'),
            horaInicio: $celda.data('inicio'),
            horaFin: $celda.data('fin'),
            idModulo: datos.modulo,
            idProfesor: datos.profesor,
            idAula: idAula
        }, null, 'json').done(function (resp) {
            if (resp && resp.ok) {
                pintarAsignada($celda, datos, idAula);
            } else {
                alert(resp && resp.msg ? resp.msg : 'No se pudo guardar.');
            }
        }).fail(function () {
            alert('Error de conexión al guardar.');
        });
    }

    // Cambia solo el aula de una celda ya asignada
    function cambiarAula($sel) {
        var $celda = $sel.closest('.horario-celda');
        var $box = $sel.closest('.horario-asignado');
        var previo = $sel.data('prev') || '';
        $.post(rutaControlador('guardar.php'), {
            csrf_token: csrf,
            idCiclo: idCiclo,
            dia: $celda.data('dia'),
            horaInicio: $celda.data('inicio'),
            horaFin: $celda.data('fin'),
            idModulo: $box.data('modulo'),
            idProfesor: $box.data('profesor'),
            idAula: $sel.val()
        }, null, 'json').done(function (resp) {
            if (resp && resp.ok) {
                $sel.data('prev', $sel.val());
            } else {
                alert(resp && resp.msg ? resp.msg : 'No se pudo asignar el aula.');
                $sel.val(previo);
            }
        }).fail(function () {
            alert('Error de conexión al asignar el aula.');
            $sel.val(previo);
        });
    }

    function limpiar($celda) {
        $.post(rutaControlador('borrar.php'), {
            csrf_token: csrf,
            idCiclo: idCiclo,
            dia: $celda.data('dia'),
            horaInicio: $celda.data('inicio')
        }, null, 'json').done(function (resp) {
            if (resp && resp.ok) {
                pintarVacia($celda);
            } else {
                alert(resp && resp.msg ? resp.msg : 'No se pudo eliminar.');
            }
        }).fail(function () {
            alert('Error de conexión al eliminar.');
        });
    }

    function datosDeTarjeta($tarjeta) {
        return {
            modulo: $tarjeta.data('modulo'),
            profesor: $tarjeta.data('profesor'),
            moduloNombre: $tarjeta.data('modulo-nombre'),
            profesorNombre: $tarjeta.data('profesor-nombre'),
            color: $tarjeta.data('color')
        };
    }

    function init() {
        $app = $('#horarioApp');
        if (!$app.length) return;
        idCiclo = $app.data('ciclo');
        csrf = $app.attr('data-csrf');

        // Buscador en vivo
        $('#horarioBuscar').on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            $('.horario-tarjeta').each(function () {
                var mod = ($(this).data('modulo-nombre') + '').toLowerCase();
                var prof = ($(this).data('profesor-nombre') + '').toLowerCase();
                $(this).toggle(mod.indexOf(q) !== -1 || prof.indexOf(q) !== -1);
            });
        });

        // Seleccion por clic
        $app.on('click', '.horario-tarjeta', function () {
            if ($(this).hasClass('seleccionada')) {
                $(this).removeClass('seleccionada');
                seleccionada = null;
            } else {
                $('.horario-tarjeta').removeClass('seleccionada');
                $(this).addClass('seleccionada');
                seleccionada = $(this);
            }
        });

        // Clic en celda -> asignar la tarjeta seleccionada
        $app.on('click', '.horario-celda', function (e) {
            if ($(e.target).closest('.horario-limpiar').length) return;
            if ($(e.target).closest('.horario-aula-select').length) return;
            if (seleccionada) {
                asignar($(this), datosDeTarjeta(seleccionada));
            }
        });

        // Boton limpiar
        $app.on('click', '.horario-limpiar', function (e) {
            e.stopPropagation();
            limpiar($(this).closest('.horario-celda'));
        });

        // Aula: recordar valor previo y guardar al cambiar
        $app.on('focus', '.horario-aula-select', function () {
            $(this).data('prev', $(this).val());
        });
        $app.on('change', '.horario-aula-select', function (e) {
            e.stopPropagation();
            cambiarAula($(this));
        });

        // Drag & drop (escritorio)
        $app.on('dragstart', '.horario-tarjeta', function (e) {
            e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify(datosDeTarjeta($(this))));
        });
        $app.on('dragover', '.horario-celda', function (e) {
            e.preventDefault();
            $(this).addClass('horario-celda-drop');
        });
        $app.on('dragleave drop', '.horario-celda', function () {
            $(this).removeClass('horario-celda-drop');
        });
        $app.on('drop', '.horario-celda', function (e) {
            e.preventDefault();
            try {
                var datos = JSON.parse(e.originalEvent.dataTransfer.getData('text/plain'));
                if (datos && datos.modulo) asignar($(this), datos);
            } catch (err) { /* arrastre no válido */ }
        });
    }

    $(document).ready(init);
    return { init: init };
})();

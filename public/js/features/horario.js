/**
 * Cuadro Horario - interacciones del director/tutor (CRUD por AJAX).
 * Todo encapsulado en el objeto Horario para no contaminar el ambito global.
 * Depende de jQuery (ya cargado en el nav) y de window.HORARIO_AULAS / HORARIO_END_SLOTS.
 * Opcionalmente: window.HORARIO_CTRL_BASE para apuntar a controladores de profesor.
 */
var Horario = (function () {
    var $app, idCiclo, csrf;
    var seleccionada = null;
    var $overlay;

    function mostrarOverlay(texto) {
        if (!$overlay) $overlay = $('#horarioOverlay');
        $overlay.find('span').text(texto || 'Guardando…');
        $overlay.addClass('activo');
    }
    function ocultarOverlay() {
        if (!$overlay) $overlay = $('#horarioOverlay');
        $overlay.removeClass('activo');
    }

    function escapar(texto) {
        return $('<div>').text(texto == null ? '' : texto).html();
    }

    function notificar(msg, tipo) {
        if (window.Toast) {
            Toast.show(msg, tipo || 'error');
        } else {
            alert(msg);
        }
    }

    function rutaControlador(archivo) {
        var base = window.HORARIO_CTRL_BASE || '../../../controladores/admin/horario/';
        return base + archivo;
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
        var html =
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

    function asignar($celda, datos) {
        var idAula = $celda.find('.horario-aula-select').val() || '';
        mostrarOverlay('Guardando…');
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
            ocultarOverlay();
            if (resp && resp.ok) {
                pintarAsignada($celda, datos, idAula);
            } else {
                notificar(resp && resp.msg ? resp.msg : 'No se pudo guardar.', 'error');
            }
        }).fail(function () {
            ocultarOverlay();
            notificar('Error de conexión al guardar.', 'error');
        });
    }

    function cambiarAula($sel) {
        var $celda = $sel.closest('.horario-celda');
        var $box   = $sel.closest('.horario-asignado');
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
                notificar(resp && resp.msg ? resp.msg : 'No se pudo asignar el aula.', 'error');
                $sel.val(previo);
            }
        }).fail(function () {
            notificar('Error de conexión al asignar el aula.', 'error');
            $sel.val(previo);
        });
    }

    function limpiar($celda) {
        mostrarOverlay('Eliminando…');
        $.post(rutaControlador('borrar.php'), {
            csrf_token: csrf,
            idCiclo: idCiclo,
            dia: $celda.data('dia'),
            horaInicio: $celda.data('inicio')
        }, null, 'json').done(function (resp) {
            ocultarOverlay();
            if (resp && resp.ok) {
                pintarVacia($celda);
            } else {
                notificar(resp && resp.msg ? resp.msg : 'No se pudo eliminar.', 'error');
            }
        }).fail(function () {
            ocultarOverlay();
            notificar('Error de conexión al eliminar.', 'error');
        });
    }

    function datosDeTarjeta($tarjeta) {
        return {
            modulo:         $tarjeta.data('modulo'),
            profesor:       $tarjeta.data('profesor'),
            moduloNombre:   $tarjeta.data('modulo-nombre'),
            profesorNombre: $tarjeta.data('profesor-nombre'),
            color:          $tarjeta.data('color')
        };
    }

    // ── Franja: actualiza el select de fin cuando cambia el inicio ──
    function actualizarSelectFin(inicio) {
        var $fin  = $('#franjaFin');
        var $btn  = $('#btnAddFranja');
        $fin.empty().append('<option value="">— hora —</option>');
        if (!inicio) {
            $fin.prop('disabled', true);
            $btn.prop('disabled', true);
            return;
        }
        var parts = inicio.split(':');
        var h = parseInt(parts[0], 10), m = parseInt(parts[1], 10) + 60;
        h += Math.floor(m / 60); m = m % 60;
        var maxFin = ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2);

        var slots = window.HORARIO_END_SLOTS || [];
        slots.forEach(function (s) {
            if (s > inicio && s <= maxFin) {
                $fin.append($('<option>', { value: s, text: s }));
            }
        });
        $fin.prop('disabled', false).val('');
        $btn.prop('disabled', true);
    }

    // ── Confirmación inline para el botón quitar-franja ──
    function iniciarConfirmFranja($btn, inicio) {
        if ($btn.hasClass('hqf-confirmar')) {
            // Segunda pulsación → ejecutar
            ejecutarEliminarFranja($btn, inicio);
            return;
        }
        // Primera pulsación → pedir confirmación inline
        $btn.addClass('hqf-confirmar').attr('title', 'Pulsa de nuevo para confirmar').html(
            '<i class="fas fa-triangle-exclamation"></i> ¿Eliminar?'
        );
        // Cancelar si el usuario pulsa en otro sitio
        $(document).one('click.hqf', function (e) {
            if (!$(e.target).closest($btn).length) {
                cancelarConfirmFranja($btn);
            }
        });
    }

    function cancelarConfirmFranja($btn) {
        $btn.removeClass('hqf-confirmar').attr('title', 'Eliminar franja').html('<i class="fas fa-xmark"></i>');
        $(document).off('click.hqf');
    }

    function ejecutarEliminarFranja($btn, inicio) {
        $(document).off('click.hqf');
        $btn.removeClass('hqf-confirmar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.post(rutaControlador('removeFranja.php'), {
            csrf_token: csrf,
            idCiclo:    idCiclo,
            horaInicio: inicio
        }, null, 'json').done(function (resp) {
            if (resp && resp.ok) {
                window.location.reload();
            } else {
                $btn.prop('disabled', false).html('<i class="fas fa-xmark"></i>');
                notificar(resp && resp.msg ? resp.msg : 'No se pudo eliminar la franja.', 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-xmark"></i>');
            notificar('Error de conexión.', 'error');
        });
    }

    function init() {
        $app = $('#horarioApp');
        if (!$app.length) return;
        idCiclo = $app.data('ciclo');
        csrf    = $app.attr('data-csrf');

        // Buscador en vivo
        $('#horarioBuscar').on('input', function () {
            var q = $(this).val().toLowerCase().trim();
            $('.horario-tarjeta').each(function () {
                var mod  = ($(this).data('modulo-nombre')   + '').toLowerCase();
                var prof = ($(this).data('profesor-nombre') + '').toLowerCase();
                $(this).toggle(mod.indexOf(q) !== -1 || prof.indexOf(q) !== -1);
            });
        });

        // Seleccion por clic en tarjeta
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

        // Clic en celda → asignar tarjeta seleccionada
        $app.on('click', '.horario-celda', function (e) {
            if ($(e.target).closest('.horario-limpiar').length)    return;
            if ($(e.target).closest('.horario-aula-select').length) return;
            if (seleccionada) asignar($(this), datosDeTarjeta(seleccionada));
        });

        // Quitar módulo de una celda
        $app.on('click', '.horario-limpiar', function (e) {
            e.stopPropagation();
            limpiar($(this).closest('.horario-celda'));
        });

        // ── Eliminar franja (botón x en la cabecera de la fila) ──
        $app.on('click', '.horario-quitar-franja', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var $btn   = $(this);
            var inicio = $btn.data('inicio');
            if (!inicio) return;

            var tieneAsignados = $btn.closest('tr').find('.horario-asignado').length > 0;
            if (tieneAsignados) {
                notificar('Esta franja tiene módulos asignados. Elimínalos primero haciendo clic en × dentro de cada celda.', 'error');
                return;
            }

            iniciarConfirmFranja($btn, inicio);
        });

        // ── Añadir nueva franja: Inicio select ──
        $app.on('change', '#franjaInicio', function () {
            actualizarSelectFin($(this).val());
        });

        // ── Añadir nueva franja: Fin select ──
        $app.on('change', '#franjaFin', function () {
            $('#btnAddFranja').prop('disabled', !$(this).val());
        });

        // ── Añadir nueva franja: botón ──
        $('#btnAddFranja').on('click', function () {
            var inicio = $('#franjaInicio').val();
            var fin    = $('#franjaFin').val();
            var receso = $('#franjaReceso').is(':checked') ? 1 : 0;
            if (!inicio || !fin) {
                notificar('Selecciona la hora de inicio y fin.', 'info');
                return;
            }
            var $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando…');
            mostrarOverlay('Añadiendo franja…');
            $.post(rutaControlador('addFranja.php'), {
                csrf_token: csrf,
                idCiclo:    idCiclo,
                horaInicio: inicio,
                horaFin:    fin,
                esReceso:   receso
            }, null, 'json').done(function (resp) {
                ocultarOverlay();
                if (resp && resp.ok) {
                    window.location.reload();
                } else {
                    $btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Añadir');
                    notificar(resp && resp.msg ? resp.msg : 'No se pudo agregar la franja.', 'error');
                }
            }).fail(function () {
                ocultarOverlay();
                $btn.prop('disabled', false).html('<i class="fas fa-plus"></i> Añadir');
                notificar('Error de conexión.', 'error');
            });
        });

        // Aula: guardar valor previo + actualizar al cambiar
        $app.on('focus',  '.horario-aula-select', function ()    { $(this).data('prev', $(this).val()); });
        $app.on('change', '.horario-aula-select', function (e)   { e.stopPropagation(); cambiarAula($(this)); });

        // Drag & drop
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

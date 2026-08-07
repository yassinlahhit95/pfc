// Calendario de actividades (admin + secretaría) — modal de crear/editar
// evento sobre vistas/comunes/eventos/_gestionEventos.php. Contratos AJAX:
//   POST /controladores/{admin|secretaria}/eventos/crear.php
//   POST /controladores/{admin|secretaria}/eventos/editar.php
//   GET  /controladores/comunes/eventos/obtener.php?id=N
// (ver controladores/comunes/eventos/{crear,editar}_impl.php y obtener.php).
// El borrado usa data-modal-borrar (core/modal-borrar.js), no este fichero.
(function ($) {
    var $modal, $titulo, $id, $csrf, $rolBaseHolder, $guardar;

    function resolveAppPath(relPath) {
        if (window.AulaProResolveAppPath) return window.AulaProResolveAppPath(relPath);
        return relPath;
    }

    function rolBase() {
        return $modal.data('rol-base') || 'admin';
    }

    function actualizarAudiencePicker(tipo) {
        $('#ev-audiencia-roles').toggle(tipo === 'roles');
        $('#ev-audiencia-personalizado').toggle(tipo === 'personalizado');
    }

    function construirAudienciaJson(tipo) {
        if (tipo === 'roles') {
            var roles = [];
            $('input[name="ev-rol"]:checked').each(function () { roles.push($(this).val()); });
            return JSON.stringify({ roles: roles });
        }
        if (tipo === 'personalizado') {
            var usuarios = [];
            ($('#ev-personalizado').val() || '').split(',').forEach(function (par) {
                par = par.trim();
                if (!par || par.indexOf(':') === -1) return;
                var partes = par.split(':');
                var tipoU = (partes[0] || '').trim();
                var id = parseInt(partes[1], 10);
                if (tipoU && !isNaN(id)) usuarios.push({ id: id, tipo: tipoU });
            });
            return JSON.stringify({ usuarios_custom: usuarios });
        }
        return '';
    }

    function poblarAudiencePicker(tipoVisibilidad, audienciaJson) {
        $('input[name="ev-rol"]').prop('checked', false);
        $('#ev-personalizado').val('');

        var datos = {};
        if (audienciaJson) {
            try { datos = JSON.parse(audienciaJson) || {}; } catch (e) { datos = {}; }
        }
        if (tipoVisibilidad === 'roles' && Array.isArray(datos.roles)) {
            datos.roles.forEach(function (rol) {
                $('input[name="ev-rol"][value="' + rol + '"]').prop('checked', true);
            });
        }
        if (tipoVisibilidad === 'personalizado' && Array.isArray(datos.usuarios_custom)) {
            $('#ev-personalizado').val(datos.usuarios_custom.map(function (u) {
                return u.tipo + ':' + u.id;
            }).join(', '));
        }
        actualizarAudiencePicker(tipoVisibilidad);
    }

    function resetearForm() {
        $id.val('');
        $('#ev-titulo').val('');
        $('#ev-descripcion').val('');
        $('#ev-fecha').val(new Date().toISOString().slice(0, 10));
        $('#ev-hora').val('10:00');
        $('#ev-ubicacion').val('');
        $('input[name="ev-visibilidad"][value="publica"]').prop('checked', true);
        $('input[name="ev-rol"]').prop('checked', false);
        $('#ev-personalizado').val('');
        $('input[name="ev-recordatorio"]').prop('checked', false);
        $('input[name="ev-recordatorio"][value="24h_antes"]').prop('checked', true);
        actualizarAudiencePicker('publica');
        $('#ev-titulo-error, #ev-fecha-error').hide().text('');
    }

    function abrirModal() {
        $modal.removeClass('modal-cerrando').addClass('modal-abierto');
    }

    function cerrarModal() {
        $modal.addClass('modal-cerrando');
        setTimeout(function () { $modal.removeClass('modal-abierto modal-cerrando'); }, 180);
    }

    function abrirModalEvento(idEvento) {
        resetearForm();
        if (idEvento) {
            $titulo.text('Editar Evento');
            editarEvento(idEvento);
        } else {
            $titulo.text('Crear Evento');
            abrirModal();
        }
    }

    function editarEvento(idEvento) {
        $.ajax({
            url: resolveAppPath('controladores/comunes/eventos/obtener.php') + '?id=' + encodeURIComponent(idEvento),
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).done(function (res) {
            if (!res || !res.ok || !res.evento) {
                if (window.Toast) Toast.show((res && res.msg) || 'No se pudo cargar el evento', 'error');
                return;
            }
            var e = res.evento;
            $id.val(e.idEvento);
            $('#ev-titulo').val(e.tituloEvento || '');
            $('#ev-descripcion').val(e.descripcionEvento || '');
            $('#ev-fecha').val((e.fechaEvento || '').slice(0, 10));
            $('#ev-hora').val((e.horaEvento || '10:00:00').slice(0, 5));
            $('#ev-ubicacion').val(e.ubicacionEvento || '');

            var tipoVisibilidad = e.tipo_visibilidad || 'publica';
            $('input[name="ev-visibilidad"][value="' + tipoVisibilidad + '"]').prop('checked', true);
            poblarAudiencePicker(tipoVisibilidad, e.audiencia_json);

            $('input[name="ev-recordatorio"]').prop('checked', false);
            (res.recordatorios || []).forEach(function (r) {
                if (parseInt(r.activo, 10) === 1) {
                    $('input[name="ev-recordatorio"][value="' + r.tipo_recordatorio + '"]').prop('checked', true);
                }
            });

            abrirModal();
        }).fail(function () {
            if (window.Toast) Toast.show('Error de conexión al cargar el evento', 'error');
        });
    }

    function guardarEvento() {
        var idEvento = $id.val();
        var tipoVisibilidad = $('input[name="ev-visibilidad"]:checked').val() || 'publica';
        var recordatorios = [];
        $('input[name="ev-recordatorio"]:checked').each(function () { recordatorios.push($(this).val()); });

        var titulo = $('#ev-titulo').val().trim();
        var fecha = $('#ev-fecha').val();
        $('#ev-titulo-error, #ev-fecha-error').hide().text('');
        var invalido = false;
        if (!titulo) { $('#ev-titulo-error').text('El título es obligatorio.').show(); invalido = true; }
        if (!fecha) { $('#ev-fecha-error').text('La fecha es obligatoria.').show(); invalido = true; }
        if (invalido) return;

        var payload = {
            csrf_token: $csrf.val(),
            tituloEvento: titulo,
            descripcionEvento: $('#ev-descripcion').val().trim(),
            fechaEvento: fecha,
            horaEvento: $('#ev-hora').val() || '10:00',
            ubicacionEvento: $('#ev-ubicacion').val().trim(),
            tipo_visibilidad: tipoVisibilidad,
            audiencia_json: construirAudienciaJson(tipoVisibilidad),
            recordatorios: recordatorios
        };
        if (idEvento) payload.idEvento = idEvento;

        var url = resolveAppPath('controladores/' + rolBase() + '/eventos/' + (idEvento ? 'editar.php' : 'crear.php'));

        $guardar.prop('disabled', true).addClass('cargando');
        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).done(function (res) {
            $guardar.prop('disabled', false).removeClass('cargando');
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg || 'Evento guardado', 'success');
                cerrarModal();
                setTimeout(function () { location.reload(); }, 600);
            } else {
                if (window.Toast) Toast.show((res && res.msg) || 'Error al guardar el evento', 'error');
            }
        }).fail(function (jqXHR) {
            $guardar.prop('disabled', false).removeClass('cargando');
            if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
            if (window.Toast) Toast.show('Error de conexión al guardar', 'error');
        });
    }

    function escapeHtml(text) {
        if (window.AulaProUtils && window.AulaProUtils.escapeHtml) {
            return window.AulaProUtils.escapeHtml(text);
        }
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderizarCalendarioMensual(año, mes, eventosDelMes) {
        var fecha = new Date(año, mes, 1);
        var diasEnMes = new Date(año, mes + 1, 0).getDate();
        var primerDia = fecha.getDay();
        var eventoPorFecha = {};
        var hoy = new Date();
        var hoyStr = hoy.getFullYear() + '-' + String(hoy.getMonth() + 1).padStart(2, '0') + '-' + String(hoy.getDate()).padStart(2, '0');

        eventosDelMes = eventosDelMes || [];
        eventosDelMes.forEach(function (evt) {
            var fechaStr = (evt.fechaEvento || '').slice(0, 10);
            if (!eventoPorFecha[fechaStr]) eventoPorFecha[fechaStr] = [];
            eventoPorFecha[fechaStr].push(evt);
        });

        var html = '';
        var diaActual = 1;
        var semana = 0;

        for (semana = 0; semana < 6; semana++) {
            html += '<div class="cal-semana">';
            for (var diaSem = 0; diaSem < 7; diaSem++) {
                var esEncabezado = semana === 0;
                var dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

                if (esEncabezado) {
                    html += '<div class="cal-dia-encabezado">' + dias[diaSem] + '</div>';
                } else {
                    var esDelMes = (semana > 0 || diaSem >= primerDia) && diaActual <= diasEnMes;
                    if (semana === 0 && diaSem < primerDia) {
                        html += '<div class="cal-dia cal-dia-otra"></div>';
                    } else if (esDelMes) {
                        var fechaStr = año + '-' + String(mes + 1).padStart(2, '0') + '-' + String(diaActual).padStart(2, '0');
                        var eventosHoy = eventoPorFecha[fechaStr] || [];
                        var claseExtra = eventosHoy.length > 0 ? 'cal-dia-con-eventos' : '';
                        if (fechaStr === hoyStr) claseExtra += ' cal-dia-hoy';
                        html += '<div class="cal-dia ' + claseExtra + '" data-fecha="' + fechaStr + '">' +
                                '<div class="cal-dia-num">' + diaActual + '</div>';
                        if (eventosHoy.length > 0) {
                            html += '<div class="cal-dia-eventos">';
                            eventosHoy.slice(0, 2).forEach(function (evt) {
                                var titulo = (evt.tituloEvento || '').substring(0, 20);
                                html += '<div class="cal-evento-mini" data-editar-evento data-id="' + evt.idEvento + '">' +
                                        escapeHtml(titulo) + '</div>';
                            });
                            if (eventosHoy.length > 2) {
                                html += '<div class="cal-evento-mini cal-evento-mas">+' + (eventosHoy.length - 2) + '</div>';
                            }
                            html += '</div>';
                        }
                        html += '</div>';
                        diaActual++;
                    } else {
                        html += '<div class="cal-dia cal-dia-otra"></div>';
                    }
                }
            }
            html += '</div>';
            if (diaActual > diasEnMes) break;
        }
        return html;
    }

    function actualizarCalendarioMensual(año, mes) {
        var $widget = $('#cal-widget-mensual');
        if (!$widget.length) return;

        var start = año + '-' + String(mes + 1).padStart(2, '0') + '-01';
        var end = new Date(año, mes + 1, 0);
        var endStr = end.getFullYear() + '-' + String(end.getMonth() + 1).padStart(2, '0') + '-' + String(end.getDate()).padStart(2, '0');

        $.ajax({
            url: resolveAppPath('controladores/comunes/eventos/listar.php') + '?start=' + encodeURIComponent(start) + '&end=' + encodeURIComponent(endStr),
            type: 'GET',
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).done(function (res) {
            if (res && res.ok && Array.isArray(res.eventos)) {
                var html = renderizarCalendarioMensual(año, mes, res.eventos);
                $widget.find('.cal-mes').html(html);
                $widget.find('.cal-mes-titulo').text(
                    new Date(año, mes).toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })
                );
            }
        });
    }

    function inicializarCalendarioMensual() {
        var $widget = $('#cal-widget-mensual');
        if (!$widget.length) return;

        var hoy = new Date();
        var mesActual = hoy.getMonth();
        var añoActual = hoy.getFullYear();

        var $prevBtn = $widget.find('[data-cal-prev]');
        var $nextBtn = $widget.find('[data-cal-next]');
        var $crearBtn = $widget.find('[data-nuevo-evento]');

        $prevBtn.on('click', function (e) {
            e.preventDefault();
            mesActual--;
            if (mesActual < 0) { mesActual = 11; añoActual--; }
            actualizarCalendarioMensual(añoActual, mesActual);
        });

        $nextBtn.on('click', function (e) {
            e.preventDefault();
            mesActual++;
            if (mesActual > 11) { mesActual = 0; añoActual++; }
            actualizarCalendarioMensual(añoActual, mesActual);
        });

        $widget.on('click', '.cal-dia[data-fecha]', function (e) {
            if (!$(e.target).closest('[data-editar-evento]').length) {
                var fecha = $(this).data('fecha');
                resetearForm();
                $('#ev-fecha').val(fecha);
                $titulo.text('Crear Evento');
                abrirModal();
            }
        });

        actualizarCalendarioMensual(añoActual, mesActual);
    }

    function inicializarCalendario() {
        $modal = $('#modal-evento');
        if (!$modal.length) return;
        $titulo = $('#modal-evento-titulo');
        $id = $('#ev-id');
        $csrf = $('#ev-csrf');
        $guardar = $('#modal-evento-guardar');

        $(document).on('click', '[data-nuevo-evento]', function (e) {
            e.preventDefault();
            abrirModalEvento(null);
        });
        $(document).on('click', '[data-editar-evento]', function (e) {
            e.preventDefault();
            abrirModalEvento($(this).data('id'));
        });
        $('#modal-evento-cancelar').on('click', cerrarModal);
        $modal.on('click', function (e) { if ($(e.target).is($modal)) cerrarModal(); });
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $modal.hasClass('modal-abierto')) cerrarModal();
        });
        $('input[name="ev-visibilidad"]').on('change', function () { actualizarAudiencePicker(this.value); });
        $('#form-evento').on('submit', function (e) { e.preventDefault(); guardarEvento(); });

        inicializarCalendarioMensual();
    }

    window.abrirModalEvento = abrirModalEvento;
    window.editarEvento = editarEvento;
    window.guardarEvento = guardarEvento;
    window.actualizarAudiencePicker = actualizarAudiencePicker;
    window.inicializarCalendario = inicializarCalendario;
    window.actualizarCalendarioMensual = actualizarCalendarioMensual;

    $(document).ready(inicializarCalendario);
}(jQuery));

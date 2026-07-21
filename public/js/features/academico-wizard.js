/* ══════════════════════════════════════════════════════════════════════
   ASISTENTE DE CONFIGURACIÓN ACADÉMICA — academico-wizard.js
   Navegación por pasos + guardado AJAX de cada paso. Mismo patrón de
   petición que landing-builder.js (post() con csrf_token + X-Requested-With).
   ══════════════════════════════════════════════════════════════════════ */
(function ($) {
    'use strict';

    var BASE = '/controladores/admin/academico/';

    function csrf() { return $('#aw-csrf').val(); }
    function idConfig() { return $('#aw-idConfig').val(); }
    function toast(msg, tipo) { if (window.Toast) Toast.show(msg, tipo || 'info'); }

    // Las filas se borran en el cliente (sin recargar) — si era la última,
    // el <tbody> se queda vacío sin más. Repone la fila ".vacio" que ya
    // pinta el servidor para no dejar una tabla en blanco sin mensaje.
    function reponerVacioSiTablaQueda(idTabla, colspan, mensaje) {
        var $tbody = $('#' + idTabla + ' tbody');
        if ($tbody.length && !$tbody.find('tr').length) {
            $tbody.html('<tr><td colspan="' + colspan + '" class="vacio">' + mensaje + '</td></tr>');
        }
    }

    // Reemplaza confirm() nativo por el modal de la app; si ModalConfirm no
    // llegó a cargar por lo que sea, cae de vuelta a confirm() para no romper.
    function confirmarBorrado(mensaje, tipo) {
        if (window.ModalConfirm) return ModalConfirm.prompt(mensaje, tipo === 'info' ? 'Confirmar' : 'Confirmar eliminación', tipo);
        return $.Deferred().resolve(confirm(mensaje)).promise();
    }

    function post(accion, datos) {
        datos = $.extend({ csrf_token: csrf(), accion: accion, idConfig: idConfig() }, datos);
        return $.ajax({
            url: BASE + 'wizard.php', type: 'POST', data: datos, dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
    }

    // ── Navegación entre pasos ──
    function irAPaso(paso) {
        var $btn = $('.aw-paso-btn[data-paso="' + paso + '"]');
        if (!$btn.length) return;
        $('.aw-paso-btn').removeClass('activo');
        $btn.addClass('activo');
        $('.aw-paso').addClass('oculto');
        $('.aw-paso[data-paso="' + paso + '"]').removeClass('oculto');
    }
    $('.aw-paso-btn').on('click', function () { irAPaso($(this).data('paso')); });

    // Al recargar tras guardar un curso (?idCiclo=...#cursos), reabre esa pestaña.
    if (window.location.hash) irAPaso(window.location.hash.replace('#', ''));

    // ── PASO 1: crear / editar general ──
    $('#aw-form-crear').on('submit', function (e) {
        e.preventDefault();
        var datos = {};
        $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
        post('crear_config', datos).done(function (res) {
            if (res.ok) { toast('Configuración creada. Recargando…', 'success'); location.reload(); }
            else toast(res.msg || 'Error al crear la configuración', 'error');
        }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    $('#aw-form-general').on('submit', function (e) {
        e.preventDefault();
        var datos = {};
        $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
        post('guardar_general', datos).done(function (res) {
            toast(res.ok ? 'Guardado' : (res.msg || 'Error al guardar'), res.ok ? 'success' : 'error');
        }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    $('#aw-activar').on('click', function () {
        confirmarBorrado('¿Activar esta configuración? A partir de ahora se usará para calcular todas las notas.', 'info').then(function (ok) {
            if (!ok) return;
            post('activar', {}).done(function (res) {
                if (res.ok) { toast('Motor académico activado', 'success'); setTimeout(function () { location.reload(); }, 800); }
                else toast(res.msg || 'No se pudo activar', 'error');
            }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
        });
    });

    // ── PASO 2: cursos (la tabla se sirve ya rellena desde el servidor para
    //    el ciclo seleccionado; cambiar de ciclo recarga con ?idCiclo=) ──
    $('#aw-curso-ciclo').on('change', function () {
        window.location.href = window.location.pathname + '?idCiclo=' + encodeURIComponent($(this).val()) + '#cursos';
    });

    // El nombre del curso se elige de una lista fija (1º-4º) para evitar
    // errores de tecleo que rompan el emparejamiento con cursoAnio/anioEstudio
    // en otros formularios; "Otro" revela un campo de texto libre.
    function awAplicarPresetCurso() {
        var $preset = $('#aw-curso-nombre-preset'), $nombre = $('#aw-curso-nombre');
        if ($preset.val() === '__custom__') {
            $nombre.show().val('').trigger('focus');
        } else {
            $nombre.hide().val($preset.val());
        }
    }
    $('#aw-curso-nombre-preset').on('change', awAplicarPresetCurso);
    awAplicarPresetCurso();

    $('#aw-form-curso').on('submit', function (e) {
        e.preventDefault();
        var datos = {};
        $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
        post('guardar_curso', datos).done(function (res) {
            if (res.ok) {
                toast('Curso añadido. Recargando…', 'success');
                setTimeout(function () { window.location.href = window.location.pathname + '?idCiclo=' + encodeURIComponent(datos.idCiclo) + '#cursos'; }, 500);
            } else toast(res.msg || 'Error', 'error');
        }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    $(document).on('click', '.aw-eliminar-curso', function () {
        var $fila = $(this).closest('tr');
        confirmarBorrado('¿Eliminar este curso?').then(function (ok) {
            if (!ok) return;
            post('eliminar_curso', { idCiclo: $('#aw-curso-ciclo').val(), idCurso: $fila.data('id') }).done(function (res) {
                if (res.ok) { $fila.remove(); reponerVacioSiTablaQueda('tabla-cursos', 3, 'Este ciclo todavía no tiene cursos definidos.'); }
                else toast('No se pudo eliminar', 'error');
            });
        });
    });

    // ── PASO 3: períodos ──
    $('#aw-form-periodo').on('submit', function (e) {
        e.preventDefault();
        var datos = { visible: 0, bloqueado: 0 };
        $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
        post('guardar_periodo', datos).done(function (res) {
            if (res.ok) { toast('Período guardado. Recargando…', 'success'); setTimeout(function () { location.reload(); }, 600); }
            else toast(res.msg || 'Error', 'error');
        }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    $(document).on('click', '.aw-eliminar-periodo', function () {
        var $btn = $(this);
        confirmarBorrado('¿Eliminar este período? Las notas guardadas en él dejarán de contar.').then(function (ok) {
            if (!ok) return;
            post('eliminar_periodo', { idPeriodo: $btn.data('id') }).done(function (res) {
                if (res.ok) { $btn.closest('tr').remove(); reponerVacioSiTablaQueda('tabla-periodos', 6, 'Todavía no hay períodos académicos definidos.'); }
                else toast('No se pudo eliminar', 'error');
            });
        });
    });

    // ── PASO 4: tipos de evaluación ──
    $('#aw-form-tipo').on('submit', function (e) {
        e.preventDefault();
        var datos = { obligatorio: 0, incluirEnMedia: 0 };
        $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
        post('guardar_tipo', datos).done(function (res) {
            if (res.ok) { toast('Tipo guardado. Recargando…', 'success'); setTimeout(function () { location.reload(); }, 600); }
            else toast(res.msg || 'Error', 'error');
        }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    $(document).on('click', '.aw-eliminar-tipo', function () {
        var $btn = $(this);
        confirmarBorrado('¿Eliminar este tipo de evaluación? Las notas guardadas bajo él dejarán de contar.').then(function (ok) {
            if (!ok) return;
            post('eliminar_tipo', { idTipo: $btn.data('id') }).done(function (res) {
                if (res.ok) { $btn.closest('tr').remove(); reponerVacioSiTablaQueda('tabla-tipos', 6, 'Todavía no hay tipos de evaluación definidos.'); }
                else toast('No se pudo eliminar', 'error');
            });
        });
    });

    // ── PASOS 5-8: formularios de una sola fila (checkboxes -> 0/1 explícito) ──
    function enviarFormularioSimple(selector, accion, camposCheckbox) {
        $(selector).on('submit', function (e) {
            e.preventDefault();
            var datos = {};
            camposCheckbox.forEach(function (nombreCampo) { datos[nombreCampo] = 0; });
            $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
            post(accion, datos).done(function (res) {
                toast(res.ok ? 'Guardado' : (res.msg || 'Error al guardar'), res.ok ? 'success' : 'error');
            }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
        });
    }
    enviarFormularioSimple('#aw-form-calificacion', 'guardar_calificacion', ['requiereTodosModulos']);
    enviarFormularioSimple('#aw-form-fct', 'guardar_fct', ['habilitado', 'requiereAprobarParaTitular']);
    enviarFormularioSimple('#aw-form-tfg', 'guardar_tfg', ['habilitado']);
    enviarFormularioSimple('#aw-form-retos', 'guardar_retos', []);

    // ── PASO 9: plantillas ──
    $(document).on('click', '.aw-aplicar-plantilla', function () {
        // Nombre autogenerado a partir de la plantilla — se puede renombrar
        // después desde el paso "General", sin interrumpir con un prompt() nativo.
        var nombrePlantilla = $(this).closest('tr').find('td').first().text().trim();
        var nombre = 'Copia de ' + (nombrePlantilla || 'plantilla') + ' — ' + new Date().toLocaleDateString('es-ES');
        post('aplicar_plantilla', { idPlantilla: $(this).data('id'), nombre: nombre }).done(function (res) {
            if (res.ok) { toast('Plantilla aplicada. Recargando…', 'success'); setTimeout(function () { location.reload(); }, 600); }
            else toast(res.msg || 'Error', 'error');
        }).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    $('#aw-form-guardar-plantilla').on('submit', function (e) {
        e.preventDefault();
        var datos = {};
        $(this).serializeArray().forEach(function (campo) { datos[campo.name] = campo.value; });
        post('guardar_como_plantilla', datos).done(function (res) {
            if (res.ok) { toast('Plantilla guardada', 'success'); this.reset(); } else toast(res.msg || 'Error', 'error');
        }.bind(this)).fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

})(jQuery);

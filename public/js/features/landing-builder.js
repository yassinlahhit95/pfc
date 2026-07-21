/* ══════════════════════════════════════════════════════════════════════
   CONSTRUCTOR DE LA LANDING — landing-builder.js
   Lista ordenable (DnD nativo), editor de secciones generado desde
   window.LANDING_TIPOS, subida de imágenes, publicar/descartar.
   ══════════════════════════════════════════════════════════════════════ */
(function ($) {
    'use strict';

    var TIPOS     = window.LANDING_TIPOS || {};
    var SECCIONES = window.LANDING_SECCIONES || [];
    var BASE      = '../../../controladores/admin/landing/';
    var $iframe   = $('#lb-iframe');
    var idAbierta = null;   // idSeccion abierta en el editor

    // Paleta de colores sugeridos junto a cada selector de color (acento global
    // y color de fondo/texto por sección): atajo para quien no domina hex.
    var PALETA_SUGERIDA = ['#1d4ed8', '#4338ca', '#7c3aed', '#059669', '#0891b2', '#d97706', '#dc2626', '#334155'];

    function crearSwatchesColor() {
        var $fila = $('<div class="lb-swatches">');
        PALETA_SUGERIDA.forEach(function (color) {
            $fila.append($('<button type="button" class="lb-swatch">').css('background-color', color).attr({ 'data-color': color, title: color }));
        });
        return $fila;
    }

    // Delegado: funciona tanto en el editor de secciones como en Ajustes globales.
    $(document).on('click', '.lb-swatch', function () {
        var color = $(this).data('color');
        $(this).closest('.lb-color-picker').find('input[type="color"]').val(color).trigger('input');
    });

    function csrf() { return $('#lb-csrf').val(); }

    function toast(msg, tipo) { if (window.Toast) Toast.show(msg, tipo || 'info'); }

    function post(url, datos) {
        datos = $.extend({ csrf_token: csrf() }, datos);
        return $.ajax({
            url: BASE + url, type: 'POST', data: datos, dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
    }

    function recargarPreview() {
        var iframe = $iframe.get(0);
        if (!iframe) return;
        
        fetch('../../../index.php?preview=1')
            .then(function(res) { return res.text(); })
            .then(function(html) {
                // Inyectar HTML vía srcdoc para esquivar restricciones X-Frame-Options
                // o CSP del servidor (como 'frame-ancestors none').
                iframe.srcdoc = html;
            })
            .catch(function(err) {
                console.error('Error cargando previsualización:', err);
                toast('Error al cargar previsualización', 'error');
            });
    }

    // Escuchar mensajes desde el iframe
    window.addEventListener('message', function(e) {
        // Solo aceptar mensajes del mismo origen (el iframe va en el mismo dominio)
        if (e.origin !== window.location.origin) return;
        if (e.data && e.data.action === 'edit_section') {
            var id = e.data.idSeccion;
            abrirEditor(id);
        }
    });

    function marcarCambios() {
        $('#lb-estado').attr('class', 'texto-estado azul').text('Borrador con cambios');
        $('#lb-descartar').prop('disabled', false);
    }

    function seccionPorId(id) {
        for (var i = 0; i < SECCIONES.length; i++) {
            if (SECCIONES[i].idSeccion === id) return SECCIONES[i];
        }
        return null;
    }

    /* ══════════ Reordenar (DnD nativo, patrón horario.js) ══════════ */
    var $arrastrada = null;

    $(document).on('dragstart', '.lb-item', function (e) {
        $arrastrada = $(this);
        $(this).addClass('lb-arrastrando');
        e.originalEvent.dataTransfer.effectAllowed = 'move';
        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
    });

    $(document).on('dragover', '#lb-lista .lb-item', function (e) {
        e.preventDefault();
        if (!$arrastrada || this === $arrastrada.get(0)) return;
        var rect = this.getBoundingClientRect();
        var mitad = rect.top + rect.height / 2;
        if (e.originalEvent.clientY < mitad) {
            $(this).before($arrastrada);
        } else {
            $(this).after($arrastrada);
        }
    });

    $(document).on('dragover', '#lb-lista', function (e) { e.preventDefault(); });

    $(document).on('dragend', '.lb-item', function () {
        $(this).removeClass('lb-arrastrando');
        if (!$arrastrada) return;
        $arrastrada = null;

        var ids = $('#lb-lista .lb-item').map(function () { return $(this).data('id'); }).get();
        post('reordenar.php', { orden: JSON.stringify(ids) })
            .done(function (res) {
                if (res.ok) {
                    toast(res.msg, 'success');
                    marcarCambios();
                    recargarPreview();
                } else {
                    toast(res.msg || 'Error al reordenar', 'error');
                }
            })
            .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    /* ══════════ Mostrar / ocultar sección ══════════ */
    $(document).on('click', '.lb-toggle-visible', function () {
        var $item = $(this).closest('.lb-item');
        var id = $item.data('id');
        var visible = $item.hasClass('lb-item-oculto') ? 1 : 0;
        var $boton = $(this);

        post('toggle_visible.php', { idSeccion: id, visible: visible })
            .done(function (res) {
                if (!res.ok) { toast(res.msg, 'error'); return; }
                $item.toggleClass('lb-item-oculto', res.visible !== 1);
                $boton.attr('title', res.visible === 1 ? 'Ocultar' : 'Mostrar')
                      .find('i').attr('class', res.visible === 1 ? 'fas fa-eye' : 'fas fa-eye-slash');
                toast(res.msg, 'success');
                marcarCambios();
                recargarPreview();
            })
            .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    /* ══════════ Eliminar sección ══════════ */
    $(document).on('click', '.lb-borrar-seccion', function () {
        var $item = $(this).closest('.lb-item');
        var id = $item.data('id');
        var nombre = $(this).data('nombre');
        
        var prom = window.ModalConfirm ? ModalConfirm.prompt('¿Eliminar la sección ' + nombre + '?', 'Confirmar eliminación', 'warning') : Promise.resolve(window.confirm('¿Eliminar la sección ' + nombre + '?'));
        prom.then(function(confirmed) {
            if (!confirmed) return;
            post('borrar_seccion.php', { idSeccion: id })
                .done(function (res) {
                    if (!res.ok) { toast(res.msg, 'error'); return; }
                    toast(res.msg, 'success');
                    $item.fadeOut(300, function() { $(this).remove(); });
                    marcarCambios();
                    recargarPreview();
                })
                .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
        });
    });

    /* ══════════ Añadir sección ══════════ */
    $('#lb-abrir-agregar').on('click', function () { $('#lb-modal-agregar').addClass('abierto'); });
    $('#lb-agregar-cerrar').on('click', function () { $('#lb-modal-agregar').removeClass('abierto'); });
    $('#lb-modal-agregar').on('click', function (e) { if (e.target === this) $(this).removeClass('abierto'); });

    $(document).on('click', '.lb-catalogo-item', function () {
        var tipo = $(this).data('tipo');
        post('agregar_seccion.php', { tipo: tipo })
            .done(function (res) {
                if (!res.ok) { toast(res.msg, 'error'); return; }
                toast(res.msg, 'success');
                // Recarga completa: la lista y el estado se regeneran en servidor
                setTimeout(function () { window.location.reload(); }, 500);
            })
            .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
    });

    /* ══════════ Editor de sección (formulario desde el schema) ══════════ */

    // Marca/desmarca un campo obligatorio vacío con estilo + mensaje inline.
    // Reutiliza las clases .campo-invalido/.campo-error ya definidas en estilo.css
    // para el resto de formularios del panel (agregarEstudiantes, etc.).
    function marcarCampoInvalido($campo, invalido) {
        $campo.toggleClass('campo-invalido', invalido);
        $campo.find('.campo-error').remove();
        if (invalido) $campo.append($('<span class="campo-error"><i class="fas fa-exclamation-circle"></i> Este campo es obligatorio.</span>'));
    }

    // Valida todos los campos de nivel superior marcados 'requerido' del formulario
    // abierto. Devuelve true si todo está OK; si no, marca los campos y hace foco al primero.
    function validarCamposRequeridos($form, campos) {
        var valido = true, $primero = null;
        $.each(campos, function (clave, def) {
            if (!def.requerido || def.tipo === 'lista') return;
            var $input = $form.find('[name="' + clave + '"]').first();
            var vacio = $input.val() === '' || $input.val() == null;
            marcarCampoInvalido($input.closest('.lb-ecampo'), vacio);
            if (vacio) { valido = false; if (!$primero) $primero = $input; }
        });
        if ($primero) $primero.trigger('focus');
        return valido;
    }

    function crearCampo(clave, def, valor) {
        var $campo = $('<div class="lb-ecampo">');
        var $etiqueta = $('<label>').text(def.etiqueta + (def.requerido ? ' *' : ''));
        $campo.append($etiqueta);

        switch (def.tipo) {
            case 'text':
            case 'url':
                $campo.append($('<input type="text">').attr({ name: clave, maxlength: def.max || 255 }).val(valor || ''));
                break;

            case 'textarea':
            case 'html':
                $campo.append($('<textarea rows="3">').attr({ name: clave, maxlength: def.max || 2000 }).val(valor || ''));
                break;

            case 'select':
                var $select = $('<select>').attr('name', clave);
                $.each(def.opciones || {}, function (val, texto) {
                    $select.append($('<option>').attr('value', val).text(texto));
                });
                $select.val(valor || Object.keys(def.opciones || {})[0]);
                $campo.append($select);
                break;

            case 'color':
                var $inputColor = $('<input type="color">').attr('name', clave).val(valor || '#1d4ed8');
                $campo.append($('<div class="lb-color-picker">').append($inputColor, crearSwatchesColor()));
                break;

            case 'imagen':
            case 'video':
                $campo.append(crearCampoMedia(clave, valor, def.tipo));
                break;

            case 'lista':
                $campo.append(crearCampoLista(clave, def, valor));
                break;
        }
        return $campo;
    }

    function crearCampoMedia(clave, valor, tipo) {
        var isVideo = tipo === 'video';
        var $wrap = $('<div class="lb-imagen">').attr('data-campo-imagen', clave);
        var $hidden = $('<input type="hidden">').attr('name', clave).val(valor || '');
        var $preview = $('<div class="lb-imagen-preview">');
        if (valor) {
            var srcUrl = (valor.startsWith('http://') || valor.startsWith('https://') || valor.startsWith('/')) ? valor : '/public/uploads/landing/' + valor;
            if (isVideo) {
                $preview.append($('<video controls style="max-width:100%; max-height:120px; border-radius:6px;">').attr('src', srcUrl));
            } else {
                $preview.append($('<img>').attr('src', srcUrl));
            }
        } else {
            var icon = isVideo ? 'fa-video' : 'fa-image';
            var txt = isVideo ? 'Sin vídeo' : 'Sin imagen';
            $preview.append($('<span class="lb-imagen-vacia"><i class="fas ' + icon + '"></i> ' + txt + '</span>'));
        }
        var accept = isVideo ? 'video/mp4' : 'image/jpeg,image/png,image/webp';
        var $file = $('<input type="file" accept="' + accept + '" class="lb-imagen-input" style="display:none;">');
        var $botones = $('<div class="lb-imagen-botones">')
            .append($('<button type="button" class="boton-secundario boton-pequeno lb-imagen-subir"><i class="fas fa-upload"></i> Subir</button>'))
            .append($('<button type="button" class="boton-secundario boton-pequeno lb-imagen-biblioteca"><i class="fas fa-photo-film"></i> Biblioteca</button>'))
            .append($('<button type="button" class="boton-secundario boton-pequeno lb-imagen-quitar"><i class="fas fa-xmark"></i> Quitar</button>'));
        return $wrap.append($hidden, $preview, $file, $botones);
    }

    /* ══════════ Biblioteca de imágenes/vídeos ya subidos ══════════ */
    var $wrapBibliotecaActivo = null;

    function aplicarSeleccionMedia($wrap, filename, url) {
        $wrap.find('input[type="hidden"]').val(filename);
        var isVideo = filename.toLowerCase().endsWith('.mp4');
        var $preview = $wrap.find('.lb-imagen-preview').empty();
        if (isVideo) {
            $preview.append($('<video controls style="max-width:100%; max-height:120px; border-radius:6px;">').attr('src', url));
        } else {
            $preview.append($('<img>').attr('src', url));
        }
    }

    $(document).on('click', '.lb-imagen-biblioteca', function () {
        var $wrap = $(this).closest('.lb-imagen');
        $wrapBibliotecaActivo = $wrap;
        var accept = $wrap.find('input[type="file"]').attr('accept');
        var esVideo = accept && accept.indexOf('video') !== -1;

        var $grid = $('#lb-biblioteca-grid').html('<p class="lb-biblioteca-cargando">Cargando…</p>');
        $('#lb-modal-biblioteca').addClass('abierto');

        $.getJSON(BASE + 'listar_imagenes.php', { tipo: esVideo ? 'video' : 'imagen' })
            .done(function (res) {
                if (!res.ok || !res.archivos || !res.archivos.length) {
                    $grid.html('<p class="lb-biblioteca-vacio">Todavía no has subido ningún archivo ' + (esVideo ? 'de vídeo' : 'de imagen') + '.</p>');
                    return;
                }
                $grid.empty();
                res.archivos.forEach(function (archivo) {
                    var $item = $('<button type="button" class="lb-biblioteca-item">')
                        .attr({ 'data-filename': archivo.filename, 'data-url': archivo.url });
                    if (esVideo) {
                        $item.append($('<video muted preload="metadata">').attr('src', archivo.url));
                    } else {
                        $item.append($('<img loading="lazy">').attr('src', archivo.url));
                    }
                    $grid.append($item);
                });
            })
            .fail(function () {
                $grid.html('<p class="lb-biblioteca-vacio">Error al cargar la biblioteca.</p>');
            });
    });

    $(document).on('click', '.lb-biblioteca-item', function () {
        if (!$wrapBibliotecaActivo) return;
        aplicarSeleccionMedia($wrapBibliotecaActivo, $(this).data('filename'), $(this).data('url'));
        $('#lb-modal-biblioteca').removeClass('abierto');
        $wrapBibliotecaActivo = null;
    });

    $('#lb-biblioteca-cerrar').on('click', function () {
        $('#lb-modal-biblioteca').removeClass('abierto');
        $wrapBibliotecaActivo = null;
    });
    $('#lb-modal-biblioteca').on('click', function (e) {
        if (e.target === this) { $(this).removeClass('abierto'); $wrapBibliotecaActivo = null; }
    });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#lb-modal-biblioteca').hasClass('abierto')) {
            $('#lb-modal-biblioteca').removeClass('abierto');
            $wrapBibliotecaActivo = null;
        }
    });

    // Subida de imagen inmediata al elegir archivo
    $(document).on('click', '.lb-imagen-subir', function () {
        $(this).closest('.lb-imagen').find('.lb-imagen-input').trigger('click');
    });
    $(document).on('click', '.lb-imagen-quitar', function () {
        var $wrap = $(this).closest('.lb-imagen');
        var accept = $wrap.find('input[type="file"]').attr('accept');
        var isVideo = accept && accept.includes('video');
        var icon = isVideo ? 'fa-video' : 'fa-image';
        var txt = isVideo ? 'Sin vídeo' : 'Sin imagen';
        $wrap.find('input[type="hidden"]').val('');
        $wrap.find('.lb-imagen-preview').empty()
            .append($('<span class="lb-imagen-vacia"><i class="fas ' + icon + '"></i> ' + txt + '</span>'));
    });
    $(document).on('change', '.lb-imagen-input', function () {
        var archivo = this.files && this.files[0];
        if (!archivo) return;
        var $wrap = $(this).closest('.lb-imagen');
        var fd = new FormData();
        fd.append('imagen', archivo);
        fd.append('csrf_token', csrf());
        
        $wrap.addClass('lb-subiendo');
        var $preview = $wrap.find('.lb-imagen-preview');
        var oldHtml = $preview.html();
        $preview.empty().append($('<span class="lb-imagen-vacia"><i class="fas fa-spinner fa-spin"></i> Subiendo archivo...</span>'));

        $.ajax({
            url: BASE + 'subir_imagen.php', type: 'POST', data: fd,
            processData: false, contentType: false, dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            if (!res.ok) { 
                toast(res.msg, 'error'); 
                $preview.html(oldHtml);
                return; 
            }
            $wrap.find('input[type="hidden"]').val(res.filename);
            if (res.filename.endsWith('.mp4')) {
                $wrap.find('.lb-imagen-preview').empty().append($('<video controls style="max-width:100%; max-height:120px; border-radius:6px;">').attr('src', res.url));
            } else {
                $wrap.find('.lb-imagen-preview').empty().append($('<img>').attr('src', res.url));
            }
            toast(res.msg, 'success');
        })
        .fail(function (jqXHR) {
            // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
            if (!(jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500)) {
                toast('Error al subir la imagen', 'error');
            }
            $preview.html(oldHtml);
        })
        .always(function () { $wrap.removeClass('lb-subiendo'); });
        $(this).val('');
    });

    function crearCampoLista(clave, def, valor) {
        var items = Array.isArray(valor) ? valor : [];
        var $wrap = $('<div class="lb-elista">').attr({ 'data-campo-lista': clave, 'data-max': def.max || 10 });
        var $items = $('<div class="lb-elista-items">');
        items.forEach(function (item) { $items.append(crearItemLista(def, item)); });
        var $agregar = $('<button type="button" class="boton-secundario boton-pequeno lb-elista-agregar">')
            .html('<i class="fas fa-plus"></i> Añadir elemento');
        return $wrap.append($items, $agregar);
    }

    function crearItemLista(def, item) {
        var $tarjeta = $('<div class="lb-elista-item">');
        var $barra = $('<div class="lb-elista-barra">')
            .append($('<button type="button" class="lb-item-btn lb-elista-subir" title="Subir"><i class="fas fa-arrow-up"></i></button>'))
            .append($('<button type="button" class="lb-item-btn lb-elista-bajar" title="Bajar"><i class="fas fa-arrow-down"></i></button>'))
            .append($('<button type="button" class="lb-item-btn lb-item-btn-peligro lb-elista-quitar" title="Quitar"><i class="fas fa-trash"></i></button>'));
        $tarjeta.append($barra);
        $.each(def.subcampos || {}, function (subClave, subDef) {
            $tarjeta.append(crearCampo(subClave, subDef, (item || {})[subClave]));
        });
        return $tarjeta;
    }

    $(document).on('click', '.lb-elista-agregar', function () {
        var $wrap = $(this).closest('.lb-elista');
        var max = parseInt($wrap.data('max'), 10) || 10;
        var $items = $wrap.find('> .lb-elista-items');
        if ($items.children().length >= max) {
            toast('Máximo ' + max + ' elementos en esta lista.', 'error');
            return;
        }
        var clave = $wrap.data('campo-lista');
        var seccion = seccionPorId(idAbierta);
        var def = seccion ? TIPOS[seccion.tipo].campos[clave] : null;
        if (def) $items.append(crearItemLista(def, {}));
    });
    $(document).on('click', '.lb-elista-quitar', function () {
        $(this).closest('.lb-elista-item').remove();
    });
    $(document).on('click', '.lb-elista-subir', function () {
        var $item = $(this).closest('.lb-elista-item');
        $item.prev('.lb-elista-item').before($item);
    });
    $(document).on('click', '.lb-elista-bajar', function () {
        var $item = $(this).closest('.lb-elista-item');
        $item.next('.lb-elista-item').after($item);
    });

    // Campos que el motor añade automáticamente a TODOS los tipos de sección
    // (engine/secciones.php); se agrupan aparte para no alargar el formulario.
    var CAMPOS_AVANZADOS = ['navVisible', 'navTexto', 'estilo_fondo', 'estilo_texto', 'estilo_fuente', 'estilo_tamano'];

    function abrirEditor(id) {
        var seccion = seccionPorId(id);
        if (!seccion || !TIPOS[seccion.tipo]) return;
        idAbierta = id;

        var tipo = TIPOS[seccion.tipo];
        $('#lb-editor-titulo').html('<i class="fas ' + tipo.icono + '"></i> ').append(document.createTextNode(tipo.nombre));

        var $form = $('#lb-editor-form').empty();
        var $avanzados = $('<div class="lb-editor-avanzados-campos">');
        $.each(tipo.campos, function (clave, def) {
            var $campo = crearCampo(clave, def, seccion.contenido[clave]);
            if (CAMPOS_AVANZADOS.indexOf(clave) !== -1) {
                $avanzados.append($campo);
            } else {
                $form.append($campo);
            }
        });
        if ($avanzados.children().length) {
            $form.append(
                $('<details class="lb-avanzado">')
                    .append('<summary><i class="fas fa-sliders"></i> Ajustes avanzados (menú y estilo)</summary>')
                    .append($avanzados)
            );
        }

        $('#lb-editor').addClass('abierto').attr('aria-hidden', 'false');
        $('#lb-editor-fondo').addClass('abierto');

        // Enviar mensaje al iframe para hacer scroll y highlight a esta sección
        var iframe = $iframe.get(0);
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage({ action: 'highlight_section', idSeccion: id }, window.location.origin);
        }
    }

    function cerrarEditor() {
        idAbierta = null;
        $('#lb-editor').removeClass('abierto').attr('aria-hidden', 'true');
        $('#lb-editor-fondo').removeClass('abierto');
    }

    // Serializa el formulario del editor según el schema del tipo
    function serializarEditor() {
        var seccion = seccionPorId(idAbierta);
        if (!seccion) return null;
        var $form = $('#lb-editor-form');
        var datos = {};

        $.each(TIPOS[seccion.tipo].campos, function (clave, def) {
            if (def.tipo === 'lista') {
                var lista = [];
                $form.find('[data-campo-lista="' + clave + '"] > .lb-elista-items > .lb-elista-item').each(function () {
                    var item = {};
                    // .bind(this) fija `this` a la tarjeta .lb-elista-item de esta
                    // iteración externa; sin él, $.each reasignaría `this` a subDef
                    // en cada vuelta del bucle interno y $(this).find(...) buscaría
                    // dentro del valor equivocado.
                    $.each(def.subcampos || {}, function (subClave) {
                        item[subClave] = $(this).find('[name="' + subClave + '"]').first().val() || '';
                    }.bind(this));
                    lista.push(item);
                });
                datos[clave] = lista;
            } else {
                datos[clave] = $form.find('[name="' + clave + '"]').first().val() || '';
            }
        });
        return datos;
    }

    $(document).on('click', '.lb-editar', function () {
        abrirEditor($(this).closest('.lb-item').data('id'));
    });
    $('#lb-editor-cerrar, #lb-editor-cancelar, #lb-editor-fondo').on('click', cerrarEditor);
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#lb-editor').hasClass('abierto')) cerrarEditor();
    });

    // Quita la marca de error en cuanto el campo deja de estar vacío.
    $('#lb-editor-form').on('input change', '.campo-invalido input, .campo-invalido textarea, .campo-invalido select', function () {
        if ($(this).val() !== '') marcarCampoInvalido($(this).closest('.lb-ecampo'), false);
    });

    $('#lb-editor-guardar').on('click', function () {
        var seccion = seccionPorId(idAbierta);
        if (!seccion) return;
        if (!validarCamposRequeridos($('#lb-editor-form'), TIPOS[seccion.tipo].campos)) {
            toast('Completa los campos obligatorios marcados en rojo.', 'error');
            return;
        }
        var datos = serializarEditor();
        if (datos === null) return;
        var id = idAbierta;
        var $boton = $(this).prop('disabled', true);

        post('guardar_seccion.php', { idSeccion: id, contenido: JSON.stringify(datos) })
            .done(function (res) {
                if (!res.ok) { toast(res.msg, 'error'); return; }
                // Actualiza la copia local en memoria (SECCIONES) para que el
                // siguiente abrirEditor()/serializarEditor() vea los datos guardados.
                var seccionActualizada = seccionPorId(id);
                if (seccionActualizada) seccionActualizada.contenido = datos;
                toast(res.msg, 'success');
                cerrarEditor();
                marcarCambios();
                recargarPreview();
            })
            .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); })
            .always(function () { $boton.prop('disabled', false); });
    });

    // Auto-guardado debounced para Live Preview
    var timeoutEditor = null;
    $('#lb-editor-form').on('input change', 'input, textarea, select', function () {
        clearTimeout(timeoutEditor);
        timeoutEditor = setTimeout(function () {
            var seccion = seccionPorId(idAbierta);
            if (!seccion) return;
            // Mientras haya un obligatorio vacío no tiene sentido autoguardar
            // (el servidor lo rechazaría entero); se limita a marcar el campo.
            if (!validarCamposRequeridos($('#lb-editor-form'), TIPOS[seccion.tipo].campos)) return;
            var datos = serializarEditor();
            if (datos === null) return;
            var id = idAbierta;
            post('guardar_seccion.php', { idSeccion: id, contenido: JSON.stringify(datos) })
                .done(function (res) {
                    if (res.ok) {
                        var seccionActualizada = seccionPorId(id);
                        if (seccionActualizada) seccionActualizada.contenido = datos;
                        marcarCambios();
                        recargarPreview();
                    } else {
                        toast(res.msg, 'error');
                    }
                });
        }, 700); // 700ms debounce
    });

    /* ══════════ Ajustes globales ══════════ */
    $('#lb-form-ajustes input[type="color"]').on('input', function () {
        $(this).siblings('code').text($(this).val());
    });

    var timeoutAjustes = null;
    $('#lb-form-ajustes').on('input change', 'input, textarea', function () {
        clearTimeout(timeoutAjustes);
        timeoutAjustes = setTimeout(function () {
            $('#lb-form-ajustes').trigger('submit', [true]); // submit de forma silenciosa
        }, 500);
    });

    $('#lb-form-ajustes').on('submit', function (e, silencioso) {
        e.preventDefault();
        post('guardar_ajustes.php', $(this).serializeArray().reduce(function (acc, campo) {
            acc[campo.name] = campo.value;
            return acc;
        }, {}))
        .done(function (res) {
            if (!res.ok) { if (!silencioso) toast(res.msg, 'error'); return; }
            if (!silencioso) toast(res.msg, 'success');
            marcarCambios();
            recargarPreview();
        })
        .fail(function (jqXHR) {
            if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
            if (!silencioso) toast('Error de conexión', 'error');
        });
    });

    /* ══════════ Publicar / descartar ══════════ */
    $('#lb-publicar').on('click', function () {
        var $boton = $(this).prop('disabled', true);
        var prom = window.ModalConfirm ? ModalConfirm.prompt('¿Publicar el borrador? Los cambios serán visibles inmediatamente en la web pública.') : Promise.resolve(window.confirm('¿Publicar el borrador? Los cambios serán visibles inmediatamente en la web pública.'));
        
        prom.then(function(confirmed) {
            if (!confirmed) {
                $boton.prop('disabled', false);
                return;
            }
            post('publicar.php', {})
                .done(function (res) {
                    if (!res.ok) { toast(res.msg, 'error'); return; }
                    toast(res.msg, 'success');
                    var ahora = new Date();
                    var fecha = ('0' + ahora.getDate()).slice(-2) + '/' + ('0' + (ahora.getMonth() + 1)).slice(-2) + '/' +
                                ahora.getFullYear() + ' ' + ('0' + ahora.getHours()).slice(-2) + ':' + ('0' + ahora.getMinutes()).slice(-2);
                    $('#lb-estado').attr('class', 'texto-estado verde').text('Publicado el ' + fecha);
                    $('#lb-descartar').prop('disabled', false);
                })
                .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); })
                .always(function () { $boton.prop('disabled', false); });
        });
    });

    $('#lb-descartar').on('click', function () {
        var prom = window.ModalConfirm ? ModalConfirm.prompt('¿Descartar los cambios del borrador y volver a la última versión publicada?') : Promise.resolve(window.confirm('¿Descartar los cambios del borrador y volver a la última versión publicada?'));
        prom.then(function(confirmed) {
            if (!confirmed) return;
            post('descartar.php', {})
                .done(function (res) {
                    if (!res.ok) { toast(res.msg, 'error'); return; }
                    toast(res.msg, 'success');
                    setTimeout(function () { window.location.reload(); }, 500);
                })
                .fail(function (jqXHR) { if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return; toast('Error de conexión', 'error'); });
        });
    });

    /* ══════════ Previsualización: dispositivos y recarga ══════════ */
    $('.lb-disp').on('click', function () {
        $('.lb-disp').removeClass('activo');
        $(this).addClass('activo');
        $iframe.css('max-width', $(this).data('ancho'));
    });
    $('#lb-recargar').on('click', recargarPreview);

    // Cargar previsualización inicial al abrir el constructor
    recargarPreview();

}(jQuery));

(function ($) {
    var $modal = null;
    var $tipo = null;
    var $nombre = null;
    var $extra = null;
    var $avisoSpan = null;
    var $confirmar = null;
    var $cancelar = null;
    var $pwWrap = null;
    var $pwInput = null;
    var $fila = null;
    var config = {};
    var DEFAULT_AVISO = 'Esta acción es permanente y no se puede deshacer.';

    function init() {
        $modal     = $('#modal-borrar');
        $tipo      = $('#modal-borrar-tipo');
        $nombre    = $('#modal-borrar-nombre');
        $extra     = $('#modal-borrar-extra');
        $avisoSpan = $('#modal-borrar-aviso');
        $confirmar = $('#modal-borrar-confirmar');
        $cancelar  = $('#modal-borrar-cancelar');
        $pwWrap    = $('#modal-password-wrap');
        $pwInput   = $('#modal-admin-password');

        if (!$modal.length) return;

        $(document).on('click', '[data-modal-borrar]', function (e) {
            e.preventDefault();
            var $btn = $(this);
            $fila = $btn.closest('tr');
            config = {
                id:               $btn.data('id'),
                tipo:             $btn.data('tipo')             || '',
                nombre:           $btn.data('nombre')           || '',
                extra:            $btn.data('extra')            || '',
                url:              $btn.data('url')              || '',
                campo:            $btn.data('campo')            || 'id',
                aviso:            $btn.data('aviso')            || DEFAULT_AVISO,
                requiresPassword: $btn.data('requires-password') === true || $btn.data('requires-password') === 'true',
                $btn:             $btn
            };
            open();
        });

        $cancelar.on('click', close);

        $modal.on('click', function (e) {
            if ($(e.target).is($modal)) close();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $modal.hasClass('open')) close();
        });

        $pwInput.on('keydown', function (e) {
            if (e.key === 'Enter') confirm();
        });

        $confirmar.on('click', confirm);
    }

    function open() {
        $tipo.text(config.tipo);
        $nombre.text(config.nombre);
        if (config.extra) {
            $extra.text(config.extra).show();
        } else {
            $extra.hide();
        }
        if ($avisoSpan && $avisoSpan.length) {
            $avisoSpan.text(config.aviso);
        }

        if (config.requiresPassword) {
            $pwInput.val('');
            $pwWrap.show();
            $modal.show();
            setTimeout(function () { $modal.addClass('open'); }, 10);
            setTimeout(function () { $pwInput.focus(); }, 280);
        } else {
            $pwWrap.hide();
            $pwInput.val('');
            $modal.show();
            setTimeout(function () { $modal.addClass('open'); }, 10);
            setTimeout(function () { $confirmar.focus(); }, 280);
        }
    }

    function close() {
        $modal.removeClass('open');
        setTimeout(function () {
            $modal.hide();
            $confirmar.prop('disabled', false).removeClass('cargando');
            $pwInput.val('');
        }, 200);
    }

    function confirm() {
        if ($confirmar.hasClass('cargando')) return;

        if (config.requiresPassword) {
            var pw = $pwInput.val().trim();
            if (!pw) {
                $pwInput.focus();
                $pwInput.css('border-color', 'var(--rojo, #ef4444)');
                setTimeout(function () { $pwInput.css('border-color', ''); }, 1500);
                return;
            }
        }

        $confirmar.prop('disabled', true).addClass('cargando');

        var data = { csrf_token: $('[name="modal_csrf"]').val() };
        data[config.campo] = config.id;
        if (config.requiresPassword) {
            data.admin_password = $pwInput.val();
        }

        $.ajax({
            url:      config.url,
            type:     'POST',
            data:     data,
            dataType: 'json',
            headers:  { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            close();
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg || 'Eliminado correctamente', 'success');
                var redirect = config.$btn ? config.$btn.data('redirect') : null;
                if (redirect) {
                    setTimeout(function () { window.location = redirect; }, 800);
                } else if ($fila && $fila.length) {
                    var tableId = $fila.closest('table').attr('id');
                    $fila.fadeOut(300, function () {
                        $(this).remove();
                        if (tableId && typeof resetearPaginacion === 'function' &&
                            typeof _paginaciones !== 'undefined' && _paginaciones[tableId]) {
                            resetearPaginacion(tableId);
                        }
                    });
                }
            } else {
                var errMsg = (res && res.msg) ? res.msg : 'Error al eliminar';
                if (window.Toast) Toast.show(errMsg, 'error');
            }
        })
        .fail(function () {
            close();
            if (window.Toast) Toast.show('Error de conexión al eliminar', 'error');
        });
    }

    $(document).ready(init);
}(jQuery));

// Diálogo de confirmación reutilizable (window.ModalConfirm), inyecta su propio
// HTML/CSS al cargar. Dos modos de uso:
//   ModalConfirm.open({url, data, onSuccess, ...})  — confirma y lanza un AJAX
//   ModalConfirm.prompt(message, title, type)        — reemplaza confirm() nativo, devuelve una Promise<boolean>
(function($) {
    if (!$ || window.ModalConfirm) return; // Solo se inicializa una vez, requiere jQuery

    // Crear la estructura HTML del modal dinámicamente
    var modalHtml = `
    <div id="global-modal-confirm" class="modal-confirm-overlay" style="display:none;">
        <div class="modal-confirm-dialog">
            <div class="modal-confirm-header">
                <i class="fas fa-exclamation-triangle modal-confirm-icon"></i>
                <h3 id="modal-confirm-title">Confirmar acción</h3>
            </div>
            <div class="modal-confirm-body">
                <p id="modal-confirm-message">¿Estás seguro de que deseas realizar esta acción?</p>
            </div>
            <div class="modal-confirm-footer">
                <button type="button" class="btn-modern btn-secondary-modern" id="modal-confirm-btn-cancel">Cancelar</button>
                <button type="button" class="btn-modern btn-danger-modern" id="modal-confirm-btn-accept">Confirmar</button>
            </div>
        </div>
    </div>
    <style>
    .modal-confirm-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);
        z-index: 9999; display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.2s ease; pointer-events: none;
    }
    .modal-confirm-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-confirm-dialog {
        background: #fff; border-radius: 16px; padding: 24px; width: 100%; max-width: 400px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(0.95) translateY(10px); transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .modal-confirm-overlay.open .modal-confirm-dialog { transform: scale(1) translateY(0); }
    .modal-confirm-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .modal-confirm-icon { font-size: 24px; color: #eab308; }
    .modal-confirm-header h3 { margin: 0; font-size: 18px; font-weight: 700; color: #1e293b; }
    .modal-confirm-body p { margin: 0; color: #475569; font-size: 15px; line-height: 1.5; }
    .modal-confirm-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .modal-confirm-footer button { padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; border: none; transition: all 0.2s; }
    #modal-confirm-btn-cancel { background: #f1f5f9; color: #475569; }
    #modal-confirm-btn-cancel:hover { background: #e2e8f0; color: #1e293b; }
    #modal-confirm-btn-accept { background: #ef4444; color: #fff; }
    #modal-confirm-btn-accept:hover { background: #dc2626; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2); }
    #modal-confirm-btn-accept.loading { opacity: 0.7; pointer-events: none; position: relative; color: transparent; }
    #modal-confirm-btn-accept.loading::after {
        content: ''; position: absolute; left: 50%; top: 50%; width: 16px; height: 16px;
        margin: -8px 0 0 -8px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
        border-radius: 50%; animation: modal-spin 0.8s linear infinite;
    }
    @keyframes modal-spin { to { transform: rotate(360deg); } }
    </style>
    `;

    var currentConfig = null;
    var $modal, $title, $msg, $btnCancel, $btnAccept;

    function init() {
        $('body').append(modalHtml);
        $modal = $('#global-modal-confirm');
        $title = $('#modal-confirm-title');
        $msg = $('#modal-confirm-message');
        $btnCancel = $('#modal-confirm-btn-cancel');
        $btnAccept = $('#modal-confirm-btn-accept');

        $modal.show(); // Display flex but opacity 0 (pointer-events none) initially

        $btnCancel.on('click', close);
        $modal.on('click', function(e) { if ($(e.target).is($modal)) close(); });
        
        $btnAccept.on('click', function() {
            if (!currentConfig) return;
            
            // Si es un prompt de UI síncrono (devuelve una promesa)
            if (currentConfig.onAccept) {
                currentConfig.onAccept();
                close();
                return;
            }

            // Si no, gestionar la lógica AJAX
            $btnAccept.addClass('loading');
            var data = currentConfig.data || {};
            var csrf = $('[name="csrf_token"]').val() || $('[name="modal_csrf"]').val() || '';
            if (csrf && !data.csrf_token) data.csrf_token = csrf;
            
            $.ajax({
                url: currentConfig.url,
                type: currentConfig.method || 'POST',
                data: data,
                dataType: 'json',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).done(function(res) {
                close();
                if (res && res.ok) {
                    if (window.Toast) Toast.show(res.msg || 'Acción completada', 'success');
                    if (currentConfig.onSuccess) currentConfig.onSuccess(res);
                    if (currentConfig.$removeElement) {
                        currentConfig.$removeElement.fadeOut(300, function() { $(this).remove(); });
                    }
                    if (currentConfig.reload) {
                        setTimeout(function() { window.location.reload(); }, 800);
                    }
                } else {
                    var errMsg = (res && res.msg) ? res.msg : 'Error al procesar la solicitud';
                    if (window.Toast) Toast.show(errMsg, 'error');
                }
            }).fail(function(jqXHR) {
                close();
                // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
                if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
                if (window.Toast) Toast.show('Error de red al procesar la solicitud', 'error');
            });
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $modal.hasClass('open')) close();
        });
    }

    function open(config) {
        currentConfig = config;
        $title.text(config.title || 'Confirmar acción');
        $msg.html(config.message || '¿Estás seguro?');
        $btnAccept.text(config.acceptText || 'Confirmar').removeClass('loading');
        
        if (config.type === 'info') {
            $('.modal-confirm-icon').attr('class', 'fas fa-info-circle modal-confirm-icon').css('color', '#3b82f6');
            $btnAccept.css('background', '#3b82f6');
        } else if (config.type === 'success') {
            $('.modal-confirm-icon').attr('class', 'fas fa-check-circle modal-confirm-icon').css('color', '#10b981');
            $btnAccept.css('background', '#10b981');
        } else {
            $('.modal-confirm-icon').attr('class', 'fas fa-exclamation-triangle modal-confirm-icon').css('color', '#ef4444');
            $btnAccept.css('background', ''); // use css default
        }

        $modal.addClass('open');
        setTimeout(function() { $btnCancel.focus(); }, 100);
    }

    function close() {
        $modal.removeClass('open');
        setTimeout(function() { $btnAccept.removeClass('loading'); currentConfig = null; }, 200);
    }

    // Public API
    window.ModalConfirm = {
        open: open,
        close: close,
        
        // Helper para sustituir las llamadas a confirm() nativo dentro de funciones async
        prompt: function(message, title, type) {
            return new Promise(function(resolve) {
                open({
                    message: message,
                    title: title || 'Confirmar',
                    type: type || 'warning',
                    onAccept: function() { resolve(true); }
                });
                
                // Override close momentarily to resolve false
                var wrapClose = function() {
                    resolve(false);
                    close();
                };
                $btnCancel.off('click').on('click', wrapClose);
                $modal.off('click').on('click', function(e) { if ($(e.target).is($modal)) wrapClose(); });
            });
        }
    };

    $(document).ready(init);

    // Auto-bind to elements with data-ajax-confirm
    // Captura tanto clics en enlaces como en botones
    $(document).on('click', '[data-ajax-confirm]', function(e) {
        var $el = $(this);
        // Si el atributo está en un form, que lo gestione el handler de submit
        if ($el.is('form')) return;

        e.preventDefault();
        var msg = $el.attr('data-ajax-confirm');
        var url = $el.attr('href') || $el.data('url');
        var isDelete = msg.toLowerCase().indexOf('eliminar') > -1 || msg.toLowerCase().indexOf('borrar') > -1;

        // Si es un botón de envío de formulario, interceptar el formulario
        if ($el.is('button[type="submit"]') || $el.is('input[type="submit"]')) {
            var $form = $el.closest('form');
            if ($form.length) {
                url = $form.attr('action');
                var method = $form.attr('method') || 'POST';
                var dataArr = $form.serializeArray();
                var dataObj = {};
                // Incluir el name/value del botón si lo tiene
                if ($el.attr('name')) {
                    dataObj[$el.attr('name')] = $el.val() || '';
                }
                $.each(dataArr, function() { dataObj[this.name] = this.value; });
                
                var $row = $form.closest('tr');
                if (!$row.length) $row = $form.closest('.card-ejercicio');
                if (!$row.length) $row = $form.closest('.lb-item');

                var forceReload = $el.data('reload') === true || $el.data('reload') === 'true';

                open({
                    title: isDelete ? 'Confirmar eliminación' : 'Confirmar envío',
                    message: msg,
                    url: url,
                    method: method,
                    data: dataObj,
                    $removeElement: isDelete ? $row : null,
                    reload: forceReload || !isDelete
                });
                return;
            }
        }

        // Enlace estándar
        var $row = $el.closest('tr');
        if (!$row.length) $row = $el.closest('.card-ejercicio'); // Para ítems genéricos
        if (!$row.length) $row = $el.closest('.lb-item');

        var forceReload = $el.data('reload') === true || $el.data('reload') === 'true';

        open({
            title: isDelete ? 'Confirmar eliminación' : 'Confirmar acción',
            message: msg,
            url: url,
            method: $el.data('method') || 'GET',
            $removeElement: isDelete ? $row : null,
            reload: forceReload || !isDelete
        });
    });

    // Capturar el onsubmit estándar si el propio formulario tiene el atributo
    $(document).on('submit', 'form[data-ajax-confirm]', function(e) {
        e.preventDefault();
        var $form = $(this);
        var msg = $form.attr('data-ajax-confirm');
        var url = $form.attr('action');
        var method = $form.attr('method') || 'POST';
        var dataArr = $form.serializeArray();
        var dataObj = {};
        $.each(dataArr, function() { dataObj[this.name] = this.value; });
        
        var isDelete = msg.toLowerCase().indexOf('eliminar') > -1 || msg.toLowerCase().indexOf('borrar') > -1;
        var $row = $form.closest('tr');
        if (!$row.length) $row = $form.closest('.card-ejercicio');
        if (!$row.length) $row = $form.closest('.lb-item');

        var forceReload = $form.data('reload') === true || $form.data('reload') === 'true';

        open({
            title: isDelete ? 'Confirmar eliminación' : 'Confirmar envío',
            message: msg,
            url: url,
            method: method,
            data: dataObj,
            $removeElement: isDelete ? $row : null,
            reload: forceReload || !isDelete
        });
    });

})(window.jQuery);

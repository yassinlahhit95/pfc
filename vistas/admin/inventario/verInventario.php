<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/inventario.php";

$todosLosArticulos = listarArticulos();

$titulo_pagina = "Inventario del Centro";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>Gestión de Dispositivos</h1>
    <button type="button" class="boton-primario" data-nuevo-articulo>
        <i class="fas fa-plus"></i> NUEVO DISPOSITIVO
    </button>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaInventario">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Número Serie</th>
                    <th>Cantidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todosLosArticulos)) { ?>
                    <tr><td colspan="4" class="vacio">No hay artículos en el inventario</td></tr>
                <?php } else { ?>
                    <?php foreach ($todosLosArticulos as $articulo) { ?>
                    <tr>
                        <td>
                            <?php if (!empty($articulo['foto'])): ?>
                                <img src="/public/uploads/equipos/<?= Security::escapeHtml($articulo['foto']) ?>" alt="Imagen" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            <?php else: ?>
                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><b><?= Security::escapeHtml($articulo['nombreArticulo']) ?></b></td>
                        <td><?= Security::escapeHtml($articulo['numeroSerie'] ?? '') ?></td>
                        <td><?= (int)$articulo['cantidad'] ?></td>
                        <td>
                            <?php
                            $disponibles = $articulo['cantidad'] - $articulo['prestados'];
                            $claseEstado = $disponibles > 0 ? "activo-verde" : "inactivo-rojo";
                            $textoEstado = $disponibles > 0 ? "Disponible ($disponibles)" : "Agotado";
                            ?>
                            <span class="indicador-estado <?= $claseEstado ?>">
                                <?= Security::escapeHtml($textoEstado) ?>
                            </span>
                        </td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                     <a class="recurso-menu-item" href="#"
                                        data-editar-articulo
                                        data-id="<?= (int)$articulo['idArticulo'] ?>"
                                        data-nombre="<?= Security::escapeHtml($articulo['nombreArticulo']) ?>"
                                        data-serie="<?= Security::escapeHtml($articulo['numeroSerie'] ?? '') ?>"
                                        data-estado="<?= Security::escapeHtml($articulo['estado'] ?? 'disponible') ?>"
                                        data-cantidad="<?= (int)$articulo['cantidad'] ?>"
                                        data-foto="<?= Security::escapeHtml($articulo['foto'] ?? '') ?>"><i class="fas fa-edit"></i> Editar</a>
                                     <div class="recurso-menu-sep"></div>
                                     <a class="recurso-menu-item peligro" href="#"
                                        data-modal-borrar
                                        data-id="<?= (int)$articulo['idArticulo'] ?>"
                                        data-tipo="Dispositivo"
                                        data-nombre="<?= Security::escapeHtml($articulo['nombreArticulo']) ?>"
                                       data-url="/controladores/admin/inventario/borrar.php"
                                       data-campo="idArticulo"><i class="fas fa-trash"></i> Eliminar</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Nuevo / Editar Dispositivo -->
<div id="modal-articulo" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:480px;text-align:left;">
        <h3 class="modal-titulo" id="modal-articulo-titulo" style="text-align:center;margin-bottom:18px;">Nuevo Dispositivo</h3>
        <form id="form-articulo">
            <input type="hidden" id="art-csrf" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" id="art-id" value="">
            <div class="formulario">
                <div class="campo">
                    <label for="art-nombre">Nombre del Dispositivo</label>
                    <input type="text" id="art-nombre" placeholder="Ej: Portátil HP ProBook">
                    <span class="campo-error" id="art-nombre-error" style="display:none;"></span>
                </div>
                <div class="campo">
                    <label for="art-serie">Número de Serie</label>
                    <input type="text" id="art-serie" placeholder="Ej: SN-12345678">
                    <span class="campo-error" id="art-serie-error" style="display:none;"></span>
                </div>
                <div class="campo">
                    <label for="art-cantidad">Cantidad</label>
                    <input type="number" id="art-cantidad" value="1" min="1">
                </div>
                <div class="campo" id="campo-estado" style="display:none;">
                    <label for="art-estado">Estado</label>
                    <select id="art-estado">
                        <option value="disponible">Disponible</option>
                        <option value="prestado">Prestado</option>
                        <option value="de baja">De baja</option>
                    </select>
                </div>
                <div class="campo">
                    <label for="art-foto">Fotografía (Opcional)</label>
                    <div id="preview-foto" style="margin-bottom: 10px; display: none;">
                        <img src="" id="img-preview" alt="Preview" style="max-width: 100px; border-radius: 4px;">
                    </div>
                    <input type="file" id="art-foto" name="foto" accept="image/*">
                </div>
            </div>
            <div class="modal-acciones" style="margin-top:18px;">
                <button type="button" class="boton-secundario" id="modal-articulo-cancelar">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="submit" class="boton-primario" id="modal-articulo-guardar">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaInventario', 15);

(function () {
    var $modal   = $('#modal-articulo');
    var $titulo  = $('#modal-articulo-titulo');
    var $id      = $('#art-id');
    var $nombre  = $('#art-nombre');
    var $serie   = $('#art-serie');
    var $cantidad = $('#art-cantidad');
    var $estado  = $('#art-estado');
    var $campoEstado = $('#campo-estado');
    var $previewFoto = $('#preview-foto');
    var $imgPreview  = $('#img-preview');
    var $guardar = $('#modal-articulo-guardar');

    function abrirModal(datos) {
        datos = datos || {};
        $id.val(datos.id || '');
        $nombre.val(datos.nombre || '');
        $serie.val(datos.serie || '');
        $cantidad.val(datos.cantidad || 1);
        
        if (datos.id) {
            $estado.val(datos.estado || 'disponible');
            $campoEstado.show();
        } else {
            $campoEstado.hide();
        }
        
        if (datos.foto) {
            $imgPreview.attr('src', '/public/uploads/equipos/' + datos.foto);
            $previewFoto.show();
        } else {
            $imgPreview.attr('src', '');
            $previewFoto.hide();
        }

        $('#art-nombre-error, #art-serie-error').hide().text('');
        $titulo.text(datos.id ? 'Editar Dispositivo' : 'Nuevo Dispositivo');
        $modal.removeClass('modal-cerrando').addClass('modal-abierto');
    }

    function cerrarModal() {
        $modal.addClass('modal-cerrando');
        setTimeout(function () { $modal.removeClass('modal-abierto modal-cerrando'); }, 180);
    }

    $(document).on('click', '[data-nuevo-articulo]', function (e) {
        e.preventDefault();
        abrirModal();
    });

    $(document).on('click', '[data-editar-articulo]', function (e) {
        e.preventDefault();
        var $btn = $(this);
        abrirModal({ 
            id: $btn.data('id'), 
            nombre: $btn.data('nombre'), 
            serie: $btn.data('serie'),
            estado: $btn.data('estado'),
            cantidad: $btn.data('cantidad'),
            foto: $btn.data('foto')
        });
    });

    $('#modal-articulo-cancelar').on('click', cerrarModal);
    $modal.on('click', function (e) { if ($(e.target).is($modal)) cerrarModal(); });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('modal-abierto')) cerrarModal();
    });

    $('#form-articulo').on('submit', function (e) {
        e.preventDefault();
        var idArticulo = $id.val();
        var url = idArticulo
            ? '/controladores/admin/inventario/actualizar.php'
            : '/controladores/admin/inventario/insertar.php';
        var payload = new FormData();
        payload.append('csrf_token', $('#art-csrf').val());
        payload.append('nombreArticulo', $nombre.val());
        payload.append('numeroSerie', $serie.val());
        payload.append('cantidad', $cantidad.val());
        
        if (idArticulo) {
            payload.append('estado', $estado.val());
        }
        
        var fotoFile = $('#art-foto')[0].files[0];
        if (fotoFile) {
            payload.append('foto', fotoFile);
        }

        if (idArticulo) {
            payload.append('idArticulo', idArticulo);
            payload.append('actualizarArticulo', '1');
        } else {
            payload.append('guardarArticulo', '1');
        }

        $('#art-nombre-error, #art-serie-error').hide().text('');
        $guardar.prop('disabled', true).addClass('cargando');

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            dataType: 'json',
            processData: false,
            contentType: false,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            $guardar.prop('disabled', false).removeClass('cargando');
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg, 'success');
                cerrarModal();
                setTimeout(function () { location.reload(); }, 600);
            } else {
                if (res && res.csrf_token) $('#art-csrf').val(res.csrf_token);
                if (res && res.errores) {
                    if (res.errores.nombreArticulo) $('#art-nombre-error').text(res.errores.nombreArticulo).show();
                    if (res.errores.numeroSerie) $('#art-serie-error').text(res.errores.numeroSerie).show();
                }
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al guardar', 'error');
            }
        })
        .fail(function (jqXHR) {
            $guardar.prop('disabled', false).removeClass('cargando');
            // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
            if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>


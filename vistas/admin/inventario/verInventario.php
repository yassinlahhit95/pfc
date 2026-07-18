<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/inventario.php";

$todosLosArticulos = listarArticulos();

$titulo_pagina = "AULAPRO | INVENTARIO DEL CENTRO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>GESTIÓN DE INVENTARIO</h1>
    <button type="button" class="boton-primario" data-nuevo-articulo>
        <i class="fas fa-plus"></i> NUEVO ARTÍCULO
    </button>
</div>


<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaInventario">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Número Serie</th>
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
                        <td><b><?= Security::escapeHtml($articulo['nombreArticulo']) ?></b></td>
                        <td><?= Security::escapeHtml($articulo['numeroSerie'] ?? '') ?></td>
                        <td>
                            <?php
                            $claseEstado = "activo-verde";
                            if ($articulo['estado'] != 'disponible') { $claseEstado = "inactivo-rojo"; }
                            ?>
                            <span class="indicador-estado <?= $claseEstado ?>">
                                <?= Security::escapeHtml($articulo['estado']) ?>
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
                                       data-serie="<?= Security::escapeHtml($articulo['numeroSerie'] ?? '') ?>"><i class="fas fa-edit"></i> Editar</a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$articulo['idArticulo'] ?>"
                                       data-tipo="Artículo"
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

<!-- Modal: Nuevo / Editar Artículo -->
<div id="modal-articulo" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:480px;text-align:left;">
        <h3 class="modal-titulo" id="modal-articulo-titulo" style="text-align:center;margin-bottom:18px;">Nuevo Artículo</h3>
        <form id="form-articulo">
            <input type="hidden" id="art-csrf" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" id="art-id" value="">
            <div class="formulario">
                <div class="campo">
                    <label for="art-nombre">Nombre del Artículo</label>
                    <input type="text" id="art-nombre" placeholder="Ej: Portátil HP ProBook">
                    <span class="campo-error" id="art-nombre-error" style="display:none;"></span>
                </div>
                <div class="campo">
                    <label for="art-serie">Número de Serie</label>
                    <input type="text" id="art-serie" placeholder="Ej: SN-12345678">
                    <span class="campo-error" id="art-serie-error" style="display:none;"></span>
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
    var $guardar = $('#modal-articulo-guardar');

    function abrirModal(datos) {
        datos = datos || {};
        $id.val(datos.id || '');
        $nombre.val(datos.nombre || '');
        $serie.val(datos.serie || '');
        $('#art-nombre-error, #art-serie-error').hide().text('');
        $titulo.text(datos.id ? 'Editar Artículo' : 'Nuevo Artículo');
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
        abrirModal({ id: $btn.data('id'), nombre: $btn.data('nombre'), serie: $btn.data('serie') });
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
        var payload = {
            csrf_token: $('#art-csrf').val(),
            nombreArticulo: $nombre.val(),
            numeroSerie: $serie.val()
        };
        if (idArticulo) {
            payload.idArticulo = idArticulo;
            payload.actualizarArticulo = 1;
        } else {
            payload.guardarArticulo = 1;
        }

        $('#art-nombre-error, #art-serie-error').hide().text('');
        $guardar.prop('disabled', true).addClass('cargando');

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            dataType: 'json',
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


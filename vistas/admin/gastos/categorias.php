<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_gastos');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/gastos.php";

$categorias = listarCategorias();
$gastosPorCategoria = contarGastosPorCategorias(array_column($categorias, 'idCategoria'));

$titulo_pagina = "Categorías de Gasto";
$seccion = 'gastos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Categorías de Gasto</h1>
        <p class="subtitulo-encabezado">Define las categorías y sus presupuestos anuales</p>
    </div>
    <div class="acciones-pagina">
        <a href="verGastos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Gastos</a>
        <button class="boton-primario" id="btn-nueva-categoria">
            <i class="fas fa-plus"></i> Nueva Categoría
        </button>
    </div>
</div>

<div class="panel">
    <?php if (empty($categorias)): ?>
    <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-tags"></i></div>
        <p class="panel-vacio-titulo">Sin categorías</p>
        <p class="panel-vacio-desc">Crea la primera categoría para poder registrar gastos.</p>
    </div>
    <?php else: ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCategorias">
            <thead>
                <tr>
                    <th>COLOR</th>
                    <th>NOMBRE</th>
                    <th>PRESUPUESTO ANUAL</th>
                    <th>GASTOS REGISTRADOS</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $categoria):
                    $numGastos = $gastosPorCategoria[$categoria['idCategoria']] ?? 0;
                ?>
                <tr>
                    <td>
                        <span style="display:inline-block;width:20px;height:20px;border-radius:50%;
                                     background:<?= Security::escapeHtml($categoria['color']) ?>;
                                     border:2px solid rgba(0,0,0,.08);vertical-align:middle;"></span>
                    </td>
                    <td><b><?= Security::escapeHtml($categoria['nombre']) ?></b></td>
                    <td><b><?= number_format($categoria['presupuestoAnual'], 2, ',', '.') ?> €</b></td>
                    <td>
                        <?php if ($numGastos > 0): ?>
                            <a href="verGastos.php?idCategoria=<?= (int)$categoria['idCategoria'] ?>"
                               class="texto-estado azul"><?= $numGastos ?> gasto(s)</a>
                        <?php else: ?>
                            <span class="texto-suave">Sin gastos</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="#"
                                   data-editar-categoria
                                   data-id="<?= (int)$categoria['idCategoria'] ?>"
                                   data-nombre="<?= Security::escapeHtml($categoria['nombre']) ?>"
                                   data-presupuesto="<?= Security::escapeHtml($categoria['presupuestoAnual']) ?>"
                                   data-color="<?= Security::escapeHtml($categoria['color']) ?>">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$categoria['idCategoria'] ?>"
                                   data-tipo="Categoría"
                                   data-nombre="<?= Security::escapeHtml($categoria['nombre']) ?>"
                                   data-url="/controladores/admin/categorias_gasto/borrar.php"
                                   data-campo="idCategoria"
                                   data-aviso="Solo puedes eliminarla si no tiene gastos asociados.">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Modal: Nueva / Editar Categoría -->
<div id="modal-categoria" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="text-align:left;max-width:420px;">
        <h3 class="modal-titulo" id="modal-cat-titulo" style="text-align:center;margin-bottom:18px;">Nueva Categoría</h3>
        <form id="form-categoria">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" id="cat-id" name="idCategoria" value="">
            <input type="hidden" name="insertarCategoria" id="cat-accion-insertar" value="1">
            <input type="hidden" name="actualizarCategoria" id="cat-accion-actualizar" value="">

            <div class="campo" style="margin-bottom:14px;">
                <label for="cat-nombre">Nombre <span style="color:var(--rojo)">*</span></label>
                <input type="text" id="cat-nombre" name="nombre" maxlength="100"
                       placeholder="Ej: Material Escolar" required
                       style="width:100%;padding:10px 12px;border:1.5px solid var(--border-2);border-radius:8px;background:var(--surface);color:var(--text);font-size:14px;font-family:inherit;outline:none;">
            </div>
            <div class="campo" style="margin-bottom:14px;">
                <label for="cat-presupuesto">Presupuesto anual (€)</label>
                <input type="number" id="cat-presupuesto" name="presupuestoAnual" min="0" step="0.01"
                       placeholder="0.00"
                       style="width:100%;padding:10px 12px;border:1.5px solid var(--border-2);border-radius:8px;background:var(--surface);color:var(--text);font-size:14px;font-family:inherit;outline:none;">
            </div>
            <div class="campo" style="margin-bottom:4px;">
                <label for="cat-color">Color identificativo</label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="cat-color" name="color" value="#4F46E5"
                           style="width:44px;height:44px;border:1.5px solid var(--border-2);border-radius:8px;cursor:pointer;background:none;padding:2px;">
                    <small class="texto-suave">Aparece en las gráficas de presupuesto</small>
                </div>
            </div>
            <div class="modal-acciones">
                <button type="button" id="modal-cat-cancelar" class="boton-secundario"><i class="fas fa-times"></i> Cancelar</button>
                <button type="submit" class="boton-primario" id="modal-cat-guardar"><i class="fas fa-save"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaCategorias', 15);

(function () {
    var $modal    = $('#modal-categoria');
    var $titulo   = $('#modal-cat-titulo');
    var $id       = $('#cat-id');
    var $nombre   = $('#cat-nombre');
    var $presup   = $('#cat-presupuesto');
    var $color    = $('#cat-color');
    var $accIns   = $('#cat-accion-insertar');
    var $accUpd   = $('#cat-accion-actualizar');

    function openNew() {
        $titulo.text('Nueva Categoría');
        $id.val('');
        $nombre.val('');
        $presup.val('');
        $color.val('#4F46E5');
        $accIns.attr('name', 'insertarCategoria');
        $accUpd.attr('name', '');
        $modal.removeClass('modal-cerrando').addClass('modal-abierto');
        setTimeout(function () { $nombre.focus(); }, 280);
    }

    function openEdit($btn) {
        $titulo.text('Editar Categoría');
        $id.val($btn.data('id'));
        $nombre.val($btn.data('nombre'));
        $presup.val($btn.data('presupuesto'));
        $color.val($btn.data('color'));
        $accIns.attr('name', '');
        $accUpd.attr('name', 'actualizarCategoria');
        $modal.removeClass('modal-cerrando').addClass('modal-abierto');
        setTimeout(function () { $nombre.focus(); }, 280);
    }

    function closeModal() {
        $modal.addClass('modal-cerrando');
        setTimeout(function () { $modal.removeClass('modal-abierto modal-cerrando'); }, 180);
    }

    $('#btn-nueva-categoria').on('click', openNew);
    $(document).on('click', '[data-editar-categoria]', function (e) { e.preventDefault(); openEdit($(this)); });
    $('#modal-cat-cancelar').on('click', closeModal);
    $modal.on('click', function (e) { if ($(e.target).is($modal)) closeModal(); });
    $(document).on('keydown', function (e) { if (e.key === 'Escape' && $modal.hasClass('modal-abierto')) closeModal(); });

    $('#form-categoria').on('submit', function (e) {
        e.preventDefault();
        var $btn   = $('#modal-cat-guardar');
        var isEdit = !!$id.val();
        var url    = isEdit
            ? '/controladores/admin/categorias_gasto/actualizar.php'
            : '/controladores/admin/categorias_gasto/insertar.php';

        $btn.prop('disabled', true).addClass('cargando');

        $.ajax({
            url:      url,
            type:     'POST',
            data:     $(this).serialize(),
            dataType: 'json',
            headers:  { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            $btn.prop('disabled', false).removeClass('cargando');
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg, 'success');
                closeModal();
                setTimeout(function () { location.reload(); }, 600);
            } else {
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al guardar', 'error');
            }
        })
        .fail(function (jqXHR) {
            $btn.prop('disabled', false).removeClass('cargando');
            // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
            if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>

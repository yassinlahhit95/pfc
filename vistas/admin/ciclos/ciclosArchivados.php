<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/ciclos.php";

$ciclosArchivados = listarCiclosArchivados();

$titulo_pagina = "AULAPRO | CICLOS ARCHIVADOS";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>CICLOS ARCHIVADOS</h1>
        <p class="subtitulo-encabezado">Ciclos desactivados — puedes restaurarlos o eliminarlos definitivamente</p>
    </div>
    <a href="verCiclos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a Ciclos
    </a>
</div>

<?php if (empty($ciclosArchivados)): ?>
<div class="panel-vacio">
    <div class="panel-vacio-icono"><i class="fas fa-archive"></i></div>
    <p class="panel-vacio-titulo">No hay ciclos archivados</p>
    <p class="panel-vacio-desc">Cuando archives un ciclo desde la lista, aparecerá aquí.</p>
</div>
<?php else: ?>
<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaArchivados">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ABR.</th>
                    <th>NOMBRE DEL CICLO</th>
                    <th>NIVEL</th>
                    <th>ARCHIVADO EL</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ciclosArchivados as $ciclo): ?>
                <tr>
                    <td><?= Security::escapeHtml($ciclo['idCiclo']) ?></td>
                    <td>
                        <?php if (!empty($ciclo['abreviaturaCiclo'])): ?>
                            <span class="texto-estado gris"><?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?></span>
                        <?php else: ?>
                            <span class="texto-suave">—</span>
                        <?php endif; ?>
                    </td>
                    <td><b><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></b></td>
                    <td><span class="texto-estado gris"><?= Security::escapeHtml($ciclo['nombreNivel']) ?></span></td>
                    <td>
                        <?= !empty($ciclo['fechaArchivado'])
                            ? date('d/m/Y H:i', strtotime($ciclo['fechaArchivado']))
                            : '<span class="texto-suave">—</span>' ?>
                    </td>
                    <td>
                        <div class="recurso-menu-wrap">
                            <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                            <div class="recurso-menu">
                                <a class="recurso-menu-item" href="#"
                                   data-restaurar-ciclo
                                   data-id="<?= (int)$ciclo['idCiclo'] ?>"
                                   data-nombre="<?= Security::escapeHtml($ciclo['nombreCiclo']) ?>">
                                    <i class="fas fa-undo"></i> Restaurar
                                </a>
                                <div class="recurso-menu-sep"></div>
                                <a class="recurso-menu-item peligro" href="#"
                                   data-modal-borrar
                                   data-id="<?= (int)$ciclo['idCiclo'] ?>"
                                   data-tipo="Ciclo"
                                   data-nombre="<?= Security::escapeHtml($ciclo['nombreCiclo']) ?>"
                                   data-url="/controladores/admin/ciclos/borrar.php"
                                   data-campo="idCiclo"
                                   data-requires-password="true"
                                   data-aviso="Elimina el ciclo y TODOS sus módulos, retos y calificaciones. Esta acción es permanente.">
                                    <i class="fas fa-trash"></i> Eliminar definitivamente
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaArchivados', 15);

$(document).on('click', '[data-restaurar-ciclo]', function (e) {
    e.preventDefault();
    var $btn    = $(this);
    var idCiclo = $btn.data('id');
    var nombre  = $btn.data('nombre');
    var $fila   = $btn.closest('tr');

    $btn.html('<i class="fas fa-spinner fa-spin"></i> Restaurando...');

    $.ajax({
        url:     '/controladores/admin/ciclos/restaurar.php',
        type:    'POST',
        data:    { idCiclo: idCiclo, csrf_token: $('[name="modal_csrf"]').val() },
        dataType: 'json',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .done(function (res) {
        if (res && res.ok) {
            if (window.Toast) Toast.show(res.msg, 'success');
            $fila.fadeOut(300, function () { $(this).remove(); });
        } else {
            if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al restaurar', 'error');
            $btn.html('<i class="fas fa-undo"></i> Restaurar');
        }
    })
    .fail(function (jqXHR) {
        // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
        if (!(jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500)) {
            if (window.Toast) Toast.show('Error de conexión', 'error');
        }
        $btn.html('<i class="fas fa-undo"></i> Restaurar');
    });
});
</script>

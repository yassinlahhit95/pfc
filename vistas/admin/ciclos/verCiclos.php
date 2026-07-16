<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$todosLosCiclos     = listarTodosLosCiclos();
$listaNiveles         = listarNiveles();
$todosLosProfesores = listarProfesores();
$profesoresPorCiclo   = listarProfesoresPorCiclos(array_column($todosLosCiclos, 'idCiclo'));

$titulo_pagina = "AULAPRO | CICLOS FORMATIVOS";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CICLOS FORMATIVOS</h1>
    <div class="acciones-pagina">
        <a href="ciclosArchivados.php" class="boton-secundario">
            <i class="fas fa-archive"></i> Archivados
        </a>
        <a href="agregarCiclos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO CICLO
        </a>
    </div>
</div>

<div class="panel margen-abajo">
    <div class="campo">
        <label>FILTRAR POR NIVEL:</label>
        <select id="selectFiltroNivel" onchange="filtrarTabla('selectFiltroNivel', 'tablaCiclos')">
            <option value="">-- Todos los Niveles --</option>
            <?php foreach ($listaNiveles as $nivelFiltro): ?>
                <option value="<?= Security::escapeHtml($nivelFiltro['nombreNivel']) ?>">
                    <?= Security::escapeHtml($nivelFiltro['nombreNivel']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaCiclos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ABR.</th>
                    <th>NOMBRE DEL CICLO</th>
                    <th>NIVEL</th>
                    <th>PROFESORES</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($todosLosCiclos)): ?>
                    <tr><td colspan="6" class="vacio">No hay ciclos configurados</td></tr>
                <?php else: ?>
                    <?php foreach ($todosLosCiclos as $ciclo):
                        $profsCiclo      = $profesoresPorCiclo[$ciclo['idCiclo']] ?? [];
                        $idsProfActuales = array_column($profsCiclo, 'idProfesor');
                        $nombresTutores  = array_map(['Security', 'escapeHtml'], array_column($profsCiclo, 'nombreProfesor'));
                        $textoTutores    = !empty($nombresTutores)
                            ? implode(', ', $nombresTutores)
                            : '<span class="texto-suave">Sin asignar</span>';
                    ?>
                    <tr>
                        <td><?= (int)$ciclo['idCiclo'] ?></td>
                        <td>
                            <?php if (!empty($ciclo['abreviaturaCiclo'])): ?>
                                <span class="texto-estado azul"><?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?></span>
                            <?php else: ?>
                                <span class="texto-suave">—</span>
                            <?php endif; ?>
                        </td>
                        <td><b><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></b></td>
                        <td><?= Security::escapeHtml($ciclo['nombreNivel']) ?></td>
                        <td id="prof-cell-<?= (int)$ciclo['idCiclo'] ?>"><?= $textoTutores ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="modificarCiclos.php?idCiclo=<?= (int)$ciclo['idCiclo'] ?>">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a class="recurso-menu-item" href="#"
                                       data-asignar-prof-ciclo
                                       data-id="<?= (int)$ciclo['idCiclo'] ?>"
                                       data-nombre="<?= Security::escapeHtml($ciclo['nombreCiclo']) ?>"
                                       data-prof-ids="<?= Security::escapeHtml(json_encode($idsProfActuales)) ?>">
                                        <i class="fas fa-chalkboard-teacher"></i> Asignar Profesores
                                    </a>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$ciclo['idCiclo'] ?>"
                                       data-tipo="Ciclo"
                                       data-nombre="<?= Security::escapeHtml($ciclo['nombreCiclo']) ?>"
                                       data-url="/controladores/admin/ciclos/archivar.php"
                                       data-campo="idCiclo"
                                       data-aviso="El ciclo quedará oculto del sistema. Puedes restaurarlo desde Archivados.">
                                        <i class="fas fa-archive"></i> Archivar
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Asignar Profesores al Ciclo -->
<div id="modal-asignar-prof-ciclo" class="modal-backdrop" role="dialog" aria-modal="true">
    <div class="modal-caja" style="max-width:480px;text-align:left;">
        <h3 class="modal-titulo" style="text-align:center;margin-bottom:4px;">Asignar Profesores</h3>
        <p id="modal-ciclo-nombre-label" class="texto-suave" style="text-align:center;margin-bottom:18px;font-size:13px;"></p>
        <input type="hidden" id="modal-ciclo-id-val" value="">
        <input type="hidden" id="modal-asig-ciclo-csrf" value="<?= Security::generateCSRFToken() ?>">
        <div id="modal-prof-ciclo-lista" style="max-height:300px;overflow-y:auto;border:1.5px solid var(--border-2);border-radius:10px;padding:6px 10px;">
            <?php if (empty($todosLosProfesores)): ?>
                <p class="texto-suave" style="padding:12px;text-align:center;">No hay profesores registrados.</p>
            <?php else: ?>
                <?php foreach ($todosLosProfesores as $prof): ?>
                <label style="display:flex;align-items:center;gap:10px;padding:8px 4px;cursor:pointer;border-bottom:1px solid var(--border);font-size:14px;color:var(--text);">
                    <input type="checkbox" class="prof-ciclo-check" value="<?= (int)$prof['idProfesor'] ?>"
                           style="width:16px;height:16px;accent-color:var(--accent);cursor:pointer;flex-shrink:0;">
                    <span><?= Security::escapeHtml($prof['nombreProfesor']) ?></span>
                </label>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="modal-acciones">
            <button type="button" id="modal-prof-ciclo-cancelar" class="boton-secundario">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" id="modal-prof-ciclo-guardar" class="boton-primario">
                <i class="fas fa-save"></i> Guardar
            </button>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaCiclos', 15);

(function () {
    var $modal   = $('#modal-asignar-prof-ciclo');
    var $cicloId = $('#modal-ciclo-id-val');
    var $label   = $('#modal-ciclo-nombre-label');
    var $guardar = $('#modal-prof-ciclo-guardar');

    function openModal($btn) {
        $cicloId.val($btn.data('id'));
        $label.text($btn.data('nombre'));

        var profIds = $btn.data('prof-ids') || [];
        if (typeof profIds === 'string') { try { profIds = JSON.parse(profIds); } catch(e) { profIds = []; } }

        $('.prof-ciclo-check').prop('checked', false);
        $.each(profIds, function (i, pid) {
            $('.prof-ciclo-check[value="' + pid + '"]').prop('checked', true);
        });

        $modal.removeClass('modal-cerrando').addClass('modal-abierto');
    }

    function closeModal() {
        $modal.addClass('modal-cerrando');
        setTimeout(function () { $modal.removeClass('modal-abierto modal-cerrando'); }, 180);
    }

    $(document).on('click', '[data-asignar-prof-ciclo]', function (e) {
        e.preventDefault();
        openModal($(this));
    });
    $('#modal-prof-ciclo-cancelar').on('click', closeModal);
    $modal.on('click', function (e) { if ($(e.target).is($modal)) closeModal(); });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('modal-abierto')) closeModal();
    });

    $guardar.on('click', function () {
        var idCiclo = $cicloId.val();
        var ids     = $('.prof-ciclo-check:checked').map(function () { return parseInt(this.value, 10); }).get();

        $guardar.prop('disabled', true).addClass('cargando');

        $.ajax({
            url:      '/controladores/admin/ciclos/actualizarProfesores.php',
            type:     'POST',
            data:     {
                csrf_token:    $('#modal-asig-ciclo-csrf').val(),
                idCiclo:       idCiclo,
                idsProfesores: ids
            },
            dataType: 'json',
            headers:  { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            $guardar.prop('disabled', false).removeClass('cargando');
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg, 'success');
                var txt = (res.nombres && res.nombres.length)
                    ? res.nombres.map(function (nombre) { return $('<span>').text(nombre).html(); }).join(', ')
                    : '<span class="texto-suave">Sin asignar</span>';
                $('#prof-cell-' + idCiclo).html(txt);
                $('[data-asignar-prof-ciclo][data-id="' + idCiclo + '"]').data('prof-ids', ids);
                closeModal();
            } else {
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al guardar', 'error');
            }
        })
        .fail(function () {
            $guardar.prop('disabled', false).removeClass('cargando');
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>

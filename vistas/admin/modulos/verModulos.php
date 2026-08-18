<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$listaDeModulosActuales = listarModulos();
$listaDeProfesores      = listarProfesores();
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$profesoresPorModulo = listarProfesoresPorModulos(array_column($listaDeModulosActuales, 'idModulo'));

$titulo_pagina = "Módulos Profesionales";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <div>
        <h1>Módulos Profesionales</h1>
    </div>
    <div class="acciones-pagina">
        <a href="agregarModulos.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO MÓDULO
        </a>
    </div>
</div>


<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label for="filtroNivel">FILTRAR POR NIVEL:</label>
            <select id="filtroNivel"
                    data-filtro-tabla="tablaModulos"
                    data-filtro-campo="nivel"
                    onchange="cascadeCicloSelect(this); filtrarTablaMulti('tablaModulos')">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivel) { ?>
                    <option value="<?= Security::escapeHtml($nivel['nombreNivel']) ?>">
                        <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroCiclo">FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo"
                    data-filtro-tabla="tablaModulos"
                    data-filtro-campo="ciclo"
                    onchange="filtrarTablaMulti('tablaModulos')">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= Security::escapeHtml(mb_strtoupper($cicloFiltro['nombreCiclo'], 'UTF-8')) ?>">
                        [<?= Security::escapeHtml($cicloFiltro['nombreNivel']) ?>] <?= Security::escapeHtml(mb_strtoupper($cicloFiltro['nombreCiclo'], 'UTF-8')) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroCurso">FILTRAR POR CURSO:</label>
            <select id="selectFiltroCurso"
                    data-filtro-tabla="tablaModulos"
                    data-filtro-campo="curso"
                    onchange="filtrarTablaMulti('tablaModulos')">
                <option value="">-- Todos los Cursos --</option>
                <option value="1º">1º Año</option>
                <option value="2º">2º Año</option>
            </select>
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaModulos">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CÓDIGO</th>
                    <th>NIVEL</th>
                    <th>NOMBRE DEL MÓDULO</th>
                    <th>CURSO</th>
                    <th>CICLO FORMATIVO</th>
                    <th>PROFESORES ASIGNADOS</th>
                    <th>HORAS TOTALES</th>
                    <th>ECTS</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaDeModulosActuales)) { ?>
                    <tr>
                        <td colspan="9" class="vacio">No hay módulos registrados en el sistema.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($listaDeModulosActuales as $moduloIndividual) {
                        $profsModulo         = $profesoresPorModulo[$moduloIndividual['idModulo']] ?? [];
                        $nombresProfesores   = array_column($profsModulo, 'nombreProfesor');
                        $idProfesorActual    = !empty($profsModulo) ? $profsModulo[0]['idProfesor'] : 0;
                    ?>
                    <tr>
                        <td><?= Security::escapeHtml($moduloIndividual['idModulo']) ?></td>
                        <td><?= !empty($moduloIndividual['codigoModulo']) ? Security::escapeHtml($moduloIndividual['codigoModulo']) : '<span class="texto-suave">—</span>' ?></td>
                        <td data-campo="nivel">
                            <span class="texto-estado <?= $moduloIndividual['nombreNivel'] === 'Grado Medio' ? 'azul' : 'verde' ?>"><?= Security::escapeHtml($moduloIndividual['nombreNivel']) ?></span>
                        </td>
                        <td>
                            <b><?= Security::escapeHtml(mb_strtoupper($moduloIndividual['nombreModulo'], 'UTF-8')) ?></b>
                            <?php if (!empty($moduloIndividual['tipoModulo']) && $moduloIndividual['tipoModulo'] !== 'Específico'): ?>
                                <span class="texto-pequeno" style="display:inline-block; margin-top:4px; padding:2px 6px; background:var(--surface-2); border:1px solid var(--border-2); border-radius:4px; color:var(--text-muted);">
                                    <?= Security::escapeHtml($moduloIndividual['tipoModulo']) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td data-campo="curso">
                            <span class="texto-pequeno" style="display:inline-block; padding:2px 6px; background:var(--surface-3); border-radius:4px; color:var(--text-muted);">
                                <?= Security::escapeHtml($moduloIndividual['cursoAnio'] ?? '1º') ?> Año
                            </span>
                        </td>
                        <td data-campo="ciclo">
                            <?php if (!empty($moduloIndividual['abreviaturaCiclo'])) { ?>
                                <b>[<?= Security::escapeHtml($moduloIndividual['abreviaturaCiclo']) ?>]</b>
                            <?php } ?>
                            <?= Security::escapeHtml(mb_strtoupper($moduloIndividual['nombreCiclo'], 'UTF-8')) ?>
                        </td>
                        <td id="prof-cell-<?= (int)$moduloIndividual['idModulo'] ?>">
                            <?php if (empty($nombresProfesores)) { ?>
                                <span class="texto-rojo texto-pequeno">
                                    <i class="fas fa-exclamation-triangle"></i> SIN PROFESOR
                                </span>
                            <?php } else { ?>
                                <div class="texto-pequeno">
                                    <?php
                                $listaNombres = '';
                                foreach ($nombresProfesores as $nombreProfesor) {
                                    if ($listaNombres) $listaNombres .= ', ';
                                    $listaNombres .= mb_strtoupper($nombreProfesor, 'UTF-8');
                                }
                                echo Security::escapeHtml($listaNombres);
                                ?>
                                </div>
                            <?php } ?>
                        </td>
                        <td><?= Security::escapeHtml($moduloIndividual['horasMaximas']) ?> H</td>
                        <td><?= !empty($moduloIndividual['creditosECTS']) ? Security::escapeHtml($moduloIndividual['creditosECTS']) : '<span class="texto-suave">—</span>' ?></td>
                        <td>
                            <div class="recurso-menu-wrap">
                                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                                <div class="recurso-menu">
                                    <a class="recurso-menu-item" href="#"
                                       data-modal-asignar-prof
                                       data-id-modulo="<?= (int)$moduloIndividual['idModulo'] ?>"
                                       data-nombre-modulo="<?= Security::escapeHtml($moduloIndividual['nombreModulo']) ?>"
                                       data-profesor-actual="<?= $idProfesorActual ?>">
                                       <i class="fas fa-chalkboard-teacher"></i> Asignar profesor</a>
                                    <a class="recurso-menu-item" href="modificarModulos.php?idModulo=<?= (int)$moduloIndividual['idModulo'] ?>"><i class="fas fa-edit"></i> Editar</a>
                                    
                                    <?php if (FeatureGuard::check('feature_ra_ce')): ?>
                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item" style="color:var(--f59e0b);" href="../ra_ce/gestionarRA.php?idModulo=<?= (int)$moduloIndividual['idModulo'] ?>"><i class="fas fa-star-half-stroke"></i> Eval. RA/CE</a>
                                    <?php endif; ?>

                                    <div class="recurso-menu-sep"></div>
                                    <a class="recurso-menu-item peligro" href="#"
                                       data-modal-borrar
                                       data-id="<?= (int)$moduloIndividual['idModulo'] ?>"
                                       data-tipo="Módulo"
                                       data-nombre="<?= Security::escapeHtml($moduloIndividual['nombreModulo']) ?>"
                                       data-url="/controladores/admin/modulos/borrar.php"
                                       data-campo="idModulo"><i class="fas fa-trash"></i> Eliminar</a>
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

<!-- Modal: Asignar Profesor -->
<div id="modal-asignar-prof" class="modal-backdrop" role="dialog" aria-modal="true">
  <div class="modal-caja">
    <div class="modal-icono" style="background:linear-gradient(135deg,#f0f4ff,#e0e7ff)">
      <i class="fas fa-chalkboard-teacher" style="color:var(--accent)"></i>
    </div>
    <h3 class="modal-titulo">Asignar Profesor</h3>
    <p class="modal-subtitulo">Módulo: <strong id="ap-nombre-modulo"></strong></p>
    <div style="margin:18px 0 4px;">
      <label for="ap-select-profesor" style="display:block;font-size:13px;font-weight:600;color:var(--dim);margin-bottom:8px;">Profesor asignado</label>
      <select id="ap-select-profesor" style="width:100%;padding:10px 12px;border:1.5px solid var(--border-2);border-radius:8px;background:var(--surface);color:var(--text);font-size:14px;outline:none;">
        <option value="">— Sin asignar —</option>
        <?php foreach ($listaDeProfesores as $prof): ?>
          <option value="<?= (int)$prof['idProfesor'] ?>">
            <?= Security::escapeHtml($prof['nombreProfesor']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="modal-acciones">
      <button id="ap-cancelar" class="boton-secundario"><i class="fas fa-times"></i> Cancelar</button>
      <button id="ap-guardar" class="boton-primario"><i class="fas fa-save"></i> Guardar</button>
    </div>
    <input type="hidden" id="ap-id-modulo" value="">
    <input type="hidden" name="ap_csrf" value="<?= Security::generateCSRFToken() ?>">
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
iniciarPaginacion('tablaModulos', 15);
var _ciclosData = <?= Security::jsonEncodeSafe(array_map(fn($ciclo) => [
    'valor'    => mb_strtoupper($ciclo['nombreCiclo'], 'UTF-8'),
    'label'    => '[' . $ciclo['nombreNivel'] . '] ' . mb_strtoupper($ciclo['nombreCiclo'], 'UTF-8'),
    'idNivel'  => (int)$ciclo['idNivel']
], $listaDeCiclosParaFiltro)) ?>;

function cascadeCicloSelect(selectNivel) {
    var idNivel = parseInt($(selectNivel).val(), 10) || 0;
    var $ciclo  = $('#selectFiltroCiclo');
    var prev    = $ciclo.val();

    $ciclo.find('option:not(:first)').remove();
    _ciclosData.forEach(function(cicloItem) {
        if (!idNivel || cicloItem.idNivel === idNivel) {
            $('<option>', { value: cicloItem.valor }).text(cicloItem.label).appendTo($ciclo);
        }
    });

    $ciclo.val($ciclo.find('option[value="' + prev + '"]').length ? prev : '');
}

// ── Asignar profesor modal ────────────────────────────────────────────
(function () {
    var $modal    = $('#modal-asignar-prof');
    var $select   = $('#ap-select-profesor');
    var $idModulo = $('#ap-id-modulo');
    var $nombre   = $('#ap-nombre-modulo');
    var $guardar  = $('#ap-guardar');
    var $cancelar = $('#ap-cancelar');
    var targetProfCell = null;

    function openModal($btn) {
        var idMod    = $btn.data('id-modulo');
        var nombre   = $btn.data('nombre-modulo') || '';
        var profActual = parseInt($btn.data('profesor-actual')) || 0;

        $idModulo.val(idMod);
        $nombre.text(nombre);
        $select.val(profActual || '');
        targetProfCell = document.getElementById('prof-cell-' + idMod);

        $modal.removeClass('modal-cerrando').addClass('modal-abierto');
        setTimeout(function () { $select.focus(); }, 280);
    }

    function closeModal() {
        $modal.addClass('modal-cerrando');
        setTimeout(function () {
            $modal.removeClass('modal-abierto modal-cerrando');
            $guardar.prop('disabled', false).removeClass('cargando');
        }, 180);
    }

    $(document).on('click', '[data-modal-asignar-prof]', function (e) {
        e.preventDefault();
        openModal($(this));
    });

    $cancelar.on('click', closeModal);

    $modal.on('click', function (e) {
        if ($(e.target).is($modal)) closeModal();
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('modal-abierto')) closeModal();
    });

    $guardar.on('click', function () {
        if ($guardar.hasClass('cargando')) return;
        $guardar.prop('disabled', true).addClass('cargando');

        $.ajax({
            url:     '/controladores/admin/modulos/actualizarProfesores.php',
            type:    'POST',
            data:    {
                idModulo:   $idModulo.val(),
                idProfesor: $select.val(),
                csrf_token: $('[name="ap_csrf"]').val()
            },
            dataType: 'json',
            headers:  { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            closeModal();
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg, 'success');
                if (targetProfCell) {
                    var selectedText = $select.find('option:selected').text().trim();
                    if (!$select.val()) {
                        targetProfCell.innerHTML = '<span class="texto-rojo texto-pequeno"><i class="fas fa-exclamation-triangle"></i> SIN PROFESOR</span>';
                    } else {
                        targetProfCell.innerHTML = '<div class="texto-pequeno">' + selectedText.toUpperCase() + '</div>';
                    }
                    // Actualiza el atributo de datos para futuras aperturas
                    var $link = $('[data-modal-asignar-prof][data-id-modulo="' + $idModulo.val() + '"]');
                    $link.data('profesor-actual', $select.val() || 0);
                }
            } else {
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al asignar', 'error');
            }
        })
        .fail(function (jqXHR) {
            closeModal();
            // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
            if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>


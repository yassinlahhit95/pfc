<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/grupos.php";

$titulo_pagina = "AULAPRO | LISTADO DE ASISTENCIAS";
$seccion = 'asistencias';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>REGISTRO DE ASISTENCIAS</h1>
    </div>
</div>

<?php
$listaDeCiclosParaFiltro = listarTodosLosCiclos();
$listaNiveles = listarNiveles();
$listaGruposFiltro = listarTodosLosGrupos();

$con = obtenerConexion();
$resCursosUnicos = mysqli_query($con, "SELECT nombre FROM cursos_academicos GROUP BY nombre ORDER BY MIN(orden) ASC, nombre ASC");
$aniosDisponibles = [];
if ($resCursosUnicos) {
    while ($fila = mysqli_fetch_assoc($resCursosUnicos)) {
        $aniosDisponibles[] = $fila['nombre'];
    }
}
?>

<div class="panel margen-abajo">
    <div class="caja caja-libre espacio-grande">
        <div class="campo relleno">
            <label for="selectFiltroNivel">FILTRAR POR NIVEL:</label>
            <select id="selectFiltroNivel" onchange="aplicarFiltrosAsistencias(true)">
                <option value="">-- Todos los Niveles --</option>
                <?php foreach ($listaNiveles as $nivelFiltro) { ?>
                    <option value="<?= Security::escapeHtml($nivelFiltro['idNivel']) ?>">
                        <?= Security::escapeHtml($nivelFiltro['nombreNivel']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroCiclo">FILTRAR POR CICLO:</label>
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosAsistencias(true)">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($listaDeCiclosParaFiltro as $cicloFiltro) { ?>
                    <option value="<?= Security::escapeHtml($cicloFiltro['idCiclo']) ?>">
                        <?= mb_strtoupper(Security::escapeHtml($cicloFiltro['nombreCiclo']), 'UTF-8') ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroAnio">FILTRAR POR AÑO:</label>
            <select id="selectFiltroAnio" onchange="aplicarFiltrosAsistencias(true)">
                <option value="">-- Todos los Años --</option>
                <?php foreach ($aniosDisponibles as $anioFiltro): ?>
                    <option value="<?= Security::escapeHtml($anioFiltro) ?>">
                        <?= Security::escapeHtml($anioFiltro) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="selectFiltroGrupo">FILTRAR POR GRUPO:</label>
            <select id="selectFiltroGrupo" onchange="aplicarFiltrosAsistencias(true)">
                <option value="">-- Todos los Grupos --</option>
                <?php foreach ($listaGruposFiltro as $grupoFiltro): ?>
                    <option value="<?= Security::escapeHtml($grupoFiltro['idGrupo']) ?>">
                        <?= Security::escapeHtml($grupoFiltro['nombreGrupo']) ?> (<?= Security::escapeHtml($grupoFiltro['abreviaturaCiclo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="campo relleno" style="margin-top: 15px;">
            <label for="selectFiltroEstado">ESTADO:</label>
            <select id="selectFiltroEstado" onchange="aplicarFiltrosAsistencias(true)">
                <option value="">-- Todos los estados --</option>
                <option value="presente">Presente</option>
                <option value="ausente">Ausente</option>
                <option value="retraso">Retraso</option>
                <option value="justificado">Justificado</option>
            </select>
        </div>

        <div class="campo relleno" style="margin-top: 15px;">
            <label for="inputFiltroNombre">BUSCAR ESTUDIANTE:</label>
            <input type="text" id="inputFiltroNombre" placeholder="Buscar..." oninput="debounceAplicarFiltros()" style="width: 100%;">
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaAsistencias">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>HORA</th>
                    <th>ESTADO</th>
                    <th>ESTUDIANTE</th>
                    <th>MÓDULO</th>
                    <th>PROFESOR</th>
                    <th>OBSERVACIÓN</th>
                    <th>JUSTIFICANTE</th>
                </tr>
            </thead>
            <tbody id="asistencias-tbody">
                <tr>
                    <td colspan="7" class="vacio" id="empty-state">Selecciona uno o más filtros para mostrar asistencias.</td>
                </tr>
            </tbody>
        </table>
        
        <div id="cargar-mas-container" style="text-align:center; padding: 20px; display:none;">
            <button type="button" class="boton-secundario" id="btn-cargar-mas" onclick="cargarMasAsistencias()">Cargar más</button>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
let currentOffset = 0;
const limit = 20;
let hasMore = true;
let debounceTimer;

function debounceAplicarFiltros() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(function() {
        aplicarFiltrosAsistencias(true);
    }, 500);
}

function aplicarFiltrosAsistencias(resetOffset) {
    var idNivel = $('#selectFiltroNivel').val();
    var idCiclo = $('#selectFiltroCiclo').val();
    var anio = $('#selectFiltroAnio').val();
    var idGrupo = $('#selectFiltroGrupo').val();
    var q = $('#inputFiltroNombre').val();
    var estado = $('#selectFiltroEstado').val();

    if (!idNivel && !idCiclo && !anio && !idGrupo && !q && !estado) {
        $('#asistencias-tbody').html('<tr><td colspan="7" class="vacio" id="empty-state">Selecciona uno o más filtros para mostrar asistencias.</td></tr>');
        $('#cargar-mas-container').hide();
        return;
    }

    if (resetOffset) {
        currentOffset = 0;
        $('#asistencias-tbody').html('<tr><td colspan="7" class="vacio" id="empty-state">Cargando...</td></tr>');
    }

    $.ajax({
        url: '/api/v1/attendance.php',
        type: 'GET',
        data: {
            nivel: idNivel,
            ciclo: idCiclo,
            anio: anio,
            grupo: idGrupo,
            estado: estado,
            q: q,
            limit: limit,
            offset: currentOffset
        },
        dataType: 'json'
    }).done(function(res) {
        if (resetOffset) {
            $('#asistencias-tbody').empty();
        }
        if (res && res.attendance) {
            renderAsistencias(res.attendance);
            if (res.attendance.length < limit) {
                hasMore = false;
                $('#cargar-mas-container').hide();
            } else {
                hasMore = true;
                $('#cargar-mas-container').show();
            }
            if (resetOffset && res.attendance.length === 0) {
                 $('#asistencias-tbody').html('<tr><td colspan="7" class="vacio" id="empty-state">No se encontraron asistencias con esos filtros.</td></tr>');
            }
        }
    }).fail(function() {
        if (window.Toast) Toast.show('Error al cargar asistencias', 'error');
    });
}

function cargarMasAsistencias() {
    if (!hasMore) return;
    currentOffset += limit;
    var btn = $('#btn-cargar-mas');
    var oldText = btn.text();
    btn.text('Cargando...').prop('disabled', true);
    
    aplicarFiltrosAsistencias(false);
    
    setTimeout(function() {
        btn.text(oldText).prop('disabled', false);
    }, 500);
}

function renderAsistencias(asistencias) {
    var tbody = $('#asistencias-tbody');
    var esc = window.AulaProUtils ? window.AulaProUtils.escapeHtml : function (s) { return $('<div>').text(s || '').html(); };

    asistencias.forEach(function(asist) {
        var tr = $('<tr>');

        var d = new Date(asist.fecha);
        var fechaStr = d.toLocaleDateString('es-ES') + ' ' + (asist.fechaRegistro ? asist.fechaRegistro.substring(11, 16) : '');
        tr.append('<td>' + esc(fechaStr) + '</td>');
        tr.append('<td>' + esc(asist.hora ? asist.hora.substring(0, 5) : '—') + '</td>');

        var estadoClase = 'gris';
        var estadoLabel = '—';
        if(asist.estado === 'presente') { estadoClase = 'verde'; estadoLabel = 'Presente'; }
        if(asist.estado === 'ausente') { estadoClase = 'rojo'; estadoLabel = 'Ausente'; }
        if(asist.estado === 'retraso') { estadoClase = 'naranja'; estadoLabel = 'Retraso'; }
        if(asist.estado === 'justificado') { estadoClase = 'azul'; estadoLabel = 'Justificado'; }

        tr.append('<td><span class="texto-estado ' + estadoClase + '">' + estadoLabel + '</span></td>');
        tr.append('<td><b>' + esc((asist.nombreEstudiante || '').toUpperCase()) + '</b></td>');
        tr.append('<td>' + esc(asist.nombreModulo || '') + '<br><small class="texto-suave">' + esc(asist.nombreCiclo || '') + '</small></td>');
        tr.append('<td>' + esc(asist.nombreProfesor || '') + '</td>');

        var obs = asist.observacion ? asist.observacion : (asist.justificacion ? asist.justificacion.motivo : '');
        tr.append('<td><small class="texto-suave">' + esc(obs) + '</small></td>');

        var just = '';
        if (asist.justificacion && asist.justificacion.archivo_url) {
            just = '<a href="' + esc(asist.justificacion.archivo_url) + '" target="_blank" class="boton-secundario boton-pequeno"><i class="fas fa-file-alt"></i> Ver</a>';
        }
        tr.append('<td>' + just + '</td>');

        tbody.append(tr);
    });
}
</script>

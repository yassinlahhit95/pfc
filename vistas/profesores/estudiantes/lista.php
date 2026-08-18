<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/grupos.php";

$titulo_pagina = "Listado de Estudiantes";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <div>
        <h1>Listado de Estudiantes</h1>
    </div>
    <div class="acciones-pagina">
        
        
        
        <a href="agregar.php" class="boton-primario">
            <i class="fas fa-plus"></i> NUEVO ESTUDIANTE
        </a>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="modal-import-est" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:var(--bg-1,#fff);border-radius:14px;padding:32px;width:520px;max-width:95vw;border:1px solid var(--border);">
        <h3 style="margin:0 0 8px;"><i class="fas fa-file-import"></i> Importar Estudiantes desde CSV</h3>
        <p style="font-size:.85rem;color:var(--text-2);margin-bottom:12px;">
            El CSV debe tener cabecera con estas columnas (el nombre del ciclo debe coincidir exactamente):
        </p>
        <code style="font-size:.78rem;display:block;background:var(--bg-2);padding:10px;border-radius:6px;margin-bottom:20px;word-break:break-all;">
            nombreEstudiante,emailEstudiante,dniEstudiante,telefonoEstudiante,direccionEstudiante,ciudadEstudiante,codigoPostalEstudiante,fechaNacimientoEstudiante,fechaAltaEstudiante,curso,nombreCiclo,observacionesEstudiante
        </code>
        <form action="../../../controladores/admin/estudiantes/importarCSV.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="campo">
                <label>Archivo CSV</label>
                <input type="file" name="archivo_csv" accept=".csv,text/csv" required style="width:100%;">
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="boton-primario" style="flex:1;"><i class="fas fa-upload"></i> Importar</button>
                <button type="button" class="boton-secundario" onclick="document.getElementById('modal-import-est').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<?php
$idProfesor = $_SESSION['idProfesor'] ?? '';
$esTutor = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
if ($esTutor && $idCicloTutor) {
    $cicloTutor = obtenerCicloPorId($idCicloTutor);
    $listaDeCiclosParaFiltro = $cicloTutor ? [$cicloTutor] : [];
} else {
    $listaDeCiclosParaFiltro = listarCiclosDeProfesor($idProfesor);
}
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
            <select id="selectFiltroNivel" onchange="aplicarFiltrosEstudiantes(true)">
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
            <select id="selectFiltroCiclo" onchange="aplicarFiltrosEstudiantes(true)">
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
            <select id="selectFiltroAnio" onchange="aplicarFiltrosEstudiantes(true)">
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
            <select id="selectFiltroGrupo" onchange="aplicarFiltrosEstudiantes(true)">
                <option value="">-- Todos los Grupos --</option>
                <?php foreach ($listaGruposFiltro as $grupoFiltro): ?>
                    <option value="<?= Security::escapeHtml($grupoFiltro['idGrupo']) ?>">
                        <?= Security::escapeHtml($grupoFiltro['nombreGrupo']) ?> (<?= Security::escapeHtml($grupoFiltro['abreviaturaCiclo']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno" style="margin-top: 15px;">
            <label for="inputFiltroNombre">BUSCAR POR NOMBRE:</label>
            <input type="search" id="inputFiltroNombre" placeholder="Buscar..." oninput="debounceAplicarFiltros()" autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-1p-ignore="true" data-form-type="other" style="width: 100%;">
        </div>
    </div>
</div>

<div class="panel">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEstudiantes">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NIVEL</th>
                    <th>NOMBRE COMPLETO</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th>CICLO ASIGNADO</th>
                    <th>AÑO</th>
                    <th>GRUPO / AULA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody id="estudiantes-tbody">
                <tr>
                    <td colspan="8" class="vacio" id="empty-state">Selecciona uno o más filtros para mostrar estudiantes.</td>
                </tr>
            </tbody>
        </table>
        
        <div id="cargar-mas-container" style="text-align:center; padding: 20px; display:none;">
            <button type="button" class="boton-secundario" id="btn-cargar-mas" onclick="cargarMasEstudiantes()">Cargar más</button>
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
        aplicarFiltrosEstudiantes(true);
    }, 500);
}

function aplicarFiltrosEstudiantes(resetOffset) {
    var idNivel = $('#selectFiltroNivel').val();
    var idCiclo = $('#selectFiltroCiclo').val();
    var anio = $('#selectFiltroAnio').val();
    var idGrupo = $('#selectFiltroGrupo').val();
    var q = $('#inputFiltroNombre').val();

    if (!idNivel && !idCiclo && !anio && !idGrupo && !q) {
        $('#estudiantes-tbody').html('<tr><td colspan="8" class="vacio" id="empty-state">Selecciona uno o más filtros para mostrar estudiantes.</td></tr>');
        $('#cargar-mas-container').hide();
        return;
    }

    if (resetOffset) {
        currentOffset = 0;
        $('#estudiantes-tbody').html('<tr><td colspan="8" class="vacio" id="empty-state">Cargando...</td></tr>');
    }

    $.ajax({
        url: '/api/v1/estudiantes.php',
        type: 'GET',
        data: {
            nivel: idNivel,
            ciclo: idCiclo,
            anio: anio,
            grupo: idGrupo,
            q: q,
            limit: limit,
            offset: currentOffset
        },
        dataType: 'json'
    }).done(function(res) {
        if (resetOffset) {
            $('#estudiantes-tbody').empty();
        }
        if (res && res.students) {
            renderStudents(res.students);
            if (res.students.length < limit) {
                hasMore = false;
                $('#cargar-mas-container').hide();
            } else {
                hasMore = true;
                $('#cargar-mas-container').show();
            }
            if (resetOffset && res.students.length === 0) {
                 $('#estudiantes-tbody').html('<tr><td colspan="8" class="vacio" id="empty-state">No se encontraron estudiantes con esos filtros.</td></tr>');
            }
        }
    }).fail(function() {
        if (window.Toast) Toast.show('Error al cargar estudiantes', 'error');
    });
}

function cargarMasEstudiantes() {
    if (!hasMore) return;
    currentOffset += limit;
    var btn = $('#btn-cargar-mas');
    var oldText = btn.text();
    btn.text('Cargando...').prop('disabled', true);
    
    aplicarFiltrosEstudiantes(false);
    
    setTimeout(function() {
        btn.text(oldText).prop('disabled', false);
    }, 500);
}

function renderStudents(students) {
    var tbody = $('#estudiantes-tbody');
    
    students.forEach(function(est) {
        var tr = $('<tr>').addClass('fila-nivel-' + est.idNivel).attr('data-anio', est.anioEstudio);
        
        tr.append('<td>' + est.idEstudiante + '</td>');
        tr.append('<td><span class="texto-estado ' + (est.curso === 'Grado Superior' ? 'verde' : 'azul') + '">' + (est.curso || '-') + '</span></td>');
        tr.append('<td><b>' + (est.nombreEstudiante || '').toUpperCase() + '</b></td>');
        tr.append('<td>' + (est.emailEstudiante || '') + '</td>');
        tr.append('<td>' + (est.nombreCiclo || '').toUpperCase() + '</td>');
        tr.append('<td>' + (est.anioEstudio || '-') + '</td>');
        tr.append('<td><strong>' + (est.nombreGrupo || 'Sin grupo') + '</strong></td>');
        
        var extraData = est.abreviaturaCiclo ? est.abreviaturaCiclo : (est.nombreCiclo || '');
        extraData = extraData.replace(/"/g, '&quot;');
        var nombreEst = (est.nombreEstudiante || '').replace(/"/g, '&quot;');
        
        var acciones = `
            <div class="recurso-menu-wrap">
                <button type="button" class="recurso-menu-btn" title="Opciones"><i class="fas fa-ellipsis-vertical"></i></button>
                <div class="recurso-menu">
                    <a class="recurso-menu-item" href="detalles.php?idEstudiante=${est.idEstudiante}"><i class="fas fa-id-card"></i> Ver detalles</a>
                    <a class="recurso-menu-item" href="editar.php?idEstudiante=${est.idEstudiante}"><i class="fas fa-edit"></i> Editar</a>
                    <div class="recurso-menu-sep"></div>
                    <a class="recurso-menu-item peligro" href="#"
                       data-modal-borrar
                       data-id="${est.idEstudiante}"
                       data-tipo="Estudiante"
                       data-nombre="${nombreEst}"
                       data-extra="${extraData}"
                       data-url="/controladores/profesores/estudiantes/borrar.php"
                       data-campo="idEstudiante"><i class="fas fa-trash"></i> Eliminar</a>
                </div>
            </div>
        `;
        tr.append('<td>' + acciones + '</td>');
        tbody.append(tr);
    });
}
</script>

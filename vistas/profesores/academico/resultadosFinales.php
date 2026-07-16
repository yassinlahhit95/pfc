<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor   = $_SESSION['idProfesor'] ?? '';
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

if ($esTutor && $idCicloTutor) {
    $cicloTutor       = obtenerCicloPorId($idCicloTutor);
    $todosLosCiclos = $cicloTutor ? [$cicloTutor] : [];
} else {
    $todosLosCiclos = listarCiclosDeProfesor($idProfesor);
}

$idCicloElegido = (int)($_GET['idCiclo'] ?? 0);
if ($esTutor && $idCicloTutor && !$idCicloElegido) {
    $idCicloElegido = $idCicloTutor;
}
if ($idCicloElegido) {
    $tieneAcceso = false;
    foreach ($todosLosCiclos as $ciclo) {
        if ($ciclo['idCiclo'] == $idCicloElegido) { $tieneAcceso = true; break; }
    }
    if (!$tieneAcceso) $idCicloElegido = 0;
}

$datosFinales = [];
if ($idCicloElegido) {
    $datosFinales = listarResultadosFinalesCiclo($idCicloElegido);
}

$tituloDelPagina = strtoupper("Resultados Finales - Portal Profesores");
$seccionActual   = 'resultados_finales';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>RESULTADOS FINALES DE MIS ALUMNOS</h1>
    <p class="subtitulo">Nota definitiva por módulo (mejor de convocatoria ordinaria y extraordinaria)</p>
</div>

<div class="panel">
    <div class="caja alinear-centro espacio-grande">
        <form method="GET" action="" class="relleno caja alinear-centro">
            <div class="campo relleno">
                <label for="idCiclo">Seleccione Ciclo:</label>
                <select id="idCiclo" name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($todosLosCiclos as $cicloItem) { ?>
                        <option value="<?= Security::escapeHtml($cicloItem['idCiclo']) ?>"
                            <?= ($idCicloElegido == $cicloItem['idCiclo']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml(mb_strtoupper($cicloItem['nombreCiclo'], 'UTF-8')) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php if ($idCicloElegido) { ?>
            <div class="campo relleno">
                <label for="filtroCursoEstudiante">Filtrar por Curso:</label>
                <select id="filtroCursoEstudiante" onchange="filtrarResultadosPorCurso()">
                    <option value="">-- Todos los Cursos --</option>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>
            <?php } ?>
        </form>

        <?php if (!empty($idCicloElegido) && !empty($datosFinales)) { ?>
            <form action="../../../controladores/profesores/academico/enviarNotasMasivo.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idCiclo" value="<?= Security::escapeHtml($idCicloElegido) ?>">
                <input type="submit" class="boton-primario" value="NOTIFICAR A TODOS">
            </form>
        <?php } ?>
    </div>
</div>

<?php if ($idCicloElegido) { ?>
<div class="panel margen-arriba">
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaResultados">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th title="Media de la nota definitiva por módulo (mejor convocatoria)">Media Módulos</th>
                    <th title="Media de calificaciones de retos">Media Retos</th>
                    <th title="Nota del Proyecto Final / TFG">TFG / Proyecto</th>
                    <th title="Promedio global ponderado: módulos 75 % · retos 25 % · TFG incluido si calificado">Promedio Global</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datosFinales)) { ?>
                    <tr><td colspan="6" class="vacio">No hay estudiantes en este ciclo.</td></tr>
                <?php } else {
                    foreach ($datosFinales as $fila) {
                        $estado = $fila['estado_global'];
                        $claseEstado = match($estado) {
                            'APROBADO'  => 'verde',
                            'SUSPENSO'  => 'rojo',
                            default     => 'gris',
                        };
                        $mediaModulos  = $fila['media_modulos']  !== '-' ? number_format((float)$fila['media_modulos'],  2) : '—';
                        $mediaRetos    = $fila['media_retos']    !== '-' ? number_format((float)$fila['media_retos'],    2) : '—';
                        $promedioGlobal = $fila['promedio_global'] !== '-' ? number_format((float)$fila['promedio_global'], 2) : '—';
                        $notaTFG = $fila['nota_tfg'] !== null ? Security::escapeHtml($fila['nota_tfg']) : '<span class="texto-suave">—</span>';
                ?>
                <tr class="fila-curso" data-curso="<?= Security::escapeHtml($fila['anioEstudio'] ?? '') ?>">
                    <td><b><?= Security::escapeHtml(mb_strtoupper($fila['nombreEstudiante'], 'UTF-8')) ?></b></td>
                    <td><?= Security::escapeHtml($mediaModulos) ?></td>
                    <td><?= Security::escapeHtml($mediaRetos) ?></td>
                    <td class="color-primario texto-negrita"><?= $notaTFG ?></td>
                    <td class="texto-negrita">
                        <?= Security::escapeHtml($promedioGlobal) ?>
                        <?php if (!$fila['calculo_completo']) { ?>
                            <span class="texto-estado naranja" title="No todos los módulos tienen nota registrada" style="font-size:.7rem;margin-left:4px;">Parcial</span>
                        <?php } ?>
                    </td>
                    <td>
                        <span class="texto-estado <?= $claseEstado ?>">
                            <?= Security::escapeHtml($estado) ?>
                        </span>
                        <?php if ($fila['tiene_suspensos']) { ?>
                            <span class="texto-estado rojo" title="Tiene módulos suspensos" style="font-size:.7rem;margin-left:4px;">Módulo(s) suspendido(s)</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php
                    }
                } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Desglose por módulo (colapsable por alumno) -->
<?php foreach ($datosFinales as $fila) {
    if (empty($fila['detalles_modulos'])) continue;
?>
<div class="panel margen-arriba fila-curso-detalle" data-curso="<?= Security::escapeHtml($fila['anioEstudio'] ?? '') ?>" style="font-size:.875rem;">
    <details>
        <summary style="cursor:pointer;font-weight:700;padding:10px 0;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-user" style="color:var(--accent)"></i>
            <?= Security::escapeHtml(mb_strtoupper($fila['nombreEstudiante'], 'UTF-8')) ?>
            <span class="texto-suave" style="font-weight:400;margin-left:4px;">— desglose por módulo</span>
        </summary>
        <div style="overflow-x:auto;margin-top:10px;">
            <table class="tabla-datos" style="font-size:.82rem;">
                <thead>
                    <tr>
                        <th>Módulo</th>
                        <th title="Media de notas de exámenes (nota definitiva = mejor convocatoria)">Media Exámenes</th>
                        <th>Media Retos</th>
                        <th title="Nota final del módulo: 75 % exámenes + 25 % retos">Nota Final</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($fila['detalles_modulos'] as $modulo) {
                    $claseModulo = match($modulo['estado']) {
                        'Aprobado' => 'verde',
                        'Suspenso' => 'rojo',
                        default    => 'gris',
                    };
                ?>
                    <tr>
                        <td><?= Security::escapeHtml($modulo['nombreModulo']) ?></td>
                        <td><?= Security::escapeHtml($modulo['media_notas']) ?></td>
                        <td><?= Security::escapeHtml($modulo['media_retos']) ?></td>
                        <td class="texto-negrita"><?= Security::escapeHtml($modulo['nota_final']) ?></td>
                        <td><span class="texto-estado <?= $claseModulo ?>"><?= Security::escapeHtml($modulo['estado']) ?></span></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </details>
</div>
<?php } ?>

<?php } ?>

<?php include '../comunes/footer.php'; ?>
<script>
$(function() { if (typeof iniciarPaginacion === 'function') iniciarPaginacion('tablaResultados', 30); });

function filtrarResultadosPorCurso() {
    var curso = document.getElementById('filtroCursoEstudiante').value;
    var filas = document.querySelectorAll('.fila-curso');
    filas.forEach(function(fila) {
        var optCurso = fila.getAttribute('data-curso');
        if (curso === '' || optCurso === curso) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
    var detalles = document.querySelectorAll('.fila-curso-detalle');
    detalles.forEach(function(det) {
        var optCurso = det.getAttribute('data-curso');
        if (curso === '' || optCurso === curso) {
            det.style.display = '';
        } else {
            det.style.display = 'none';
        }
    });
}
</script>

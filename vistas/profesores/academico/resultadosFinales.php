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
    $todos_los_ciclos = $cicloTutor ? [$cicloTutor] : [];
} else {
    $todos_los_ciclos = listarCiclosDeProfesor($idProfesor);
}

$id_ciclo_elegido = (int)($_GET['idCiclo'] ?? 0);
if ($esTutor && $idCicloTutor && !$id_ciclo_elegido) {
    $id_ciclo_elegido = $idCicloTutor;
}
if ($id_ciclo_elegido) {
    $tieneAcceso = false;
    foreach ($todos_los_ciclos as $c) {
        if ($c['idCiclo'] == $id_ciclo_elegido) { $tieneAcceso = true; break; }
    }
    if (!$tieneAcceso) $id_ciclo_elegido = 0;
}

$datos_finales = [];
if ($id_ciclo_elegido) {
    $datos_finales = listarResultadosFinalesCiclo($id_ciclo_elegido);
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
                    <?php foreach ($todos_los_ciclos as $cicloItem) { ?>
                        <option value="<?= Security::escapeHtml($cicloItem['idCiclo']) ?>"
                            <?= ($id_ciclo_elegido == $cicloItem['idCiclo']) ? 'selected' : '' ?>>
                            <?= Security::escapeHtml(strtoupper($cicloItem['nombreCiclo'])) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>

        <?php if (!empty($id_ciclo_elegido) && !empty($datos_finales)) { ?>
            <form action="../../../controladores/profesores/academico/enviarNotasMasivo.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idCiclo" value="<?= Security::escapeHtml($id_ciclo_elegido) ?>">
                <input type="submit" class="boton-primario" value="NOTIFICAR A TODOS">
            </form>
        <?php } ?>
    </div>
</div>

<?php if ($id_ciclo_elegido) { ?>
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
                <?php if (empty($datos_finales)) { ?>
                    <tr><td colspan="6" class="vacio">No hay estudiantes en este ciclo.</td></tr>
                <?php } else {
                    foreach ($datos_finales as $fila) {
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
                <tr>
                    <td><b><?= Security::escapeHtml(strtoupper($fila['nombreEstudiante'])) ?></b></td>
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
<?php foreach ($datos_finales as $fila) {
    if (empty($fila['detalles_modulos'])) continue;
?>
<div class="panel margen-arriba" style="font-size:.875rem;">
    <details>
        <summary style="cursor:pointer;font-weight:700;padding:10px 0;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-user" style="color:var(--accent)"></i>
            <?= Security::escapeHtml(strtoupper($fila['nombreEstudiante'])) ?>
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
                <?php foreach ($fila['detalles_modulos'] as $mod) {
                    $clsMod = match($mod['estado']) {
                        'Aprobado' => 'verde',
                        'Suspenso' => 'rojo',
                        default    => 'gris',
                    };
                ?>
                    <tr>
                        <td><?= Security::escapeHtml($mod['nombreModulo']) ?></td>
                        <td><?= Security::escapeHtml($mod['media_notas']) ?></td>
                        <td><?= Security::escapeHtml($mod['media_retos']) ?></td>
                        <td class="texto-negrita"><?= Security::escapeHtml($mod['nota_final']) ?></td>
                        <td><span class="texto-estado <?= $clsMod ?>"><?= Security::escapeHtml($mod['estado']) ?></span></td>
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
</script>

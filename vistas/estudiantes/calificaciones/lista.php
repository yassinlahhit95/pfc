<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$cursosDisponibles = obtenerCursosEscolaresEstudiante($idEstudiante);
$cursoSeleccionado = $_GET['cursoEscolar'] ?? $cursosDisponibles[0];

$resumen      = obtenerResultadosFinalesEstudiante($idEstudiante, null, $cursoSeleccionado);
$retosNotas   = listarCalificacionesRetoPorEstudiante($idEstudiante); // We didn't change retos to have cursoEscolar, they just are there

$tituloDelPagina = "AULAPRO | MIS CALIFICACIONES";
$seccionActual   = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera" style="margin-bottom:30px; display:flex; justify-content:space-between; align-items:flex-end;">
    <div>
        <h1>MIS CALIFICACIONES</h1>
        <p class="subtitulo-encabezado" style="font-size:1.1rem; opacity:0.8;"><i class="fas fa-graduation-cap"></i> <?= Security::escapeHtml($resumen['nombreCiclo'] ?? '') ?></p>
    </div>
    
    <div class="filtro-curso">
        <form action="" method="GET" style="display:flex; align-items:center; gap:10px;">
            <label for="cursoEscolar" style="font-weight:600; color:var(--text-color);">Curso Escolar:</label>
            <select name="cursoEscolar" id="cursoEscolar" onchange="this.form.submit()" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border-2); background:var(--bg-card); color:var(--text-color); outline:none;">
                <?php foreach ($cursosDisponibles as $curso) { ?>
                    <option value="<?= Security::escapeHtml($curso) ?>" <?= $curso === $cursoSeleccionado ? 'selected' : '' ?>><?= Security::escapeHtml($curso) ?></option>
                <?php } ?>
            </select>
        </form>
    </div>
</div>

<!-- ═══════════════ KPI DASHBOARD ═══════════════ -->
<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
    
    <!-- Promedio General KPI -->
    <div class="panel" style="display:flex; align-items:center; gap:20px; padding:25px;">
        <div style="width:60px; height:60px; border-radius:50%; background:rgba(79,70,229,0.1); color:var(--accent); display:flex; align-items:center; justify-content:center; font-size:1.8rem;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div>
            <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Promedio General</div>
            <div style="font-size:2.2rem; font-weight:800; color:var(--text-color); line-height:1;">
                <?= Security::escapeHtml((string)$resumen['promedio_global']) ?>
            </div>
        </div>
    </div>

    <!-- Estado Académico KPI -->
    <div class="panel" style="display:flex; align-items:center; gap:20px; padding:25px;">
        <?php
        $estadoBg    = $resumen['estado_global'] === 'APROBADO' ? 'rgba(16,185,129,0.1)' : ($resumen['estado_global'] === 'SUSPENSO' ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)');
        $estadoColor = $resumen['estado_global'] === 'APROBADO' ? '#10b981' : ($resumen['estado_global'] === 'SUSPENSO' ? '#ef4444' : '#f59e0b');
        $estadoIcono = $resumen['estado_global'] === 'APROBADO' ? 'fa-check-circle' : ($resumen['estado_global'] === 'SUSPENSO' ? 'fa-times-circle' : 'fa-clock');
        ?>
        <div style="width:60px; height:60px; border-radius:50%; background:<?= $estadoBg ?>; color:<?= $estadoColor ?>; display:flex; align-items:center; justify-content:center; font-size:1.8rem;">
            <i class="fas <?= $estadoIcono ?>"></i>
        </div>
        <div>
            <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Estado Global</div>
            <div style="font-size:1.4rem; font-weight:700; color:<?= $estadoColor ?>; line-height:1; margin-top:8px;">
                <?= Security::escapeHtml($resumen['estado_global']) ?>
            </div>
        </div>
    </div>

    <!-- TFG KPI -->
    <div class="panel" style="display:flex; align-items:center; gap:20px; padding:25px;">
        <?php
        $notaTfg = $resumen['nota_tfg'] ?? null;
        $tfgBg = 'rgba(245,158,11,0.1)'; $tfgColor = '#f59e0b'; $tfgIcono = 'fa-file-alt';
        if ($notaTfg !== null) {
            $tfgBg    = (float)$notaTfg >= 5 ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)';
            $tfgColor = (float)$notaTfg >= 5 ? '#10b981' : '#ef4444';
        }
        ?>
        <div style="width:60px; height:60px; border-radius:50%; background:<?= $tfgBg ?>; color:<?= $tfgColor ?>; display:flex; align-items:center; justify-content:center; font-size:1.8rem;">
            <i class="fas <?= $tfgIcono ?>"></i>
        </div>
        <div>
            <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; margin-bottom:5px;">Proyecto TFG</div>
            <div style="font-size:1.8rem; font-weight:800; color:var(--text-color); line-height:1; display:flex; align-items:baseline; gap:10px;">
                <?= Security::escapeHtml($notaTfg !== null ? $notaTfg : '—') ?>
                <?php if ($notaTfg !== null) { ?>
                    <span style="font-size:0.9rem; font-weight:600; color:<?= $tfgColor ?>;">/ 10</span>
                <?php } else { ?>
                    <span style="font-size:0.9rem; font-weight:600; color:var(--dim);">PENDIENTE</span>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($resumen['obs_tfg'])) { ?>
<div class="panel" style="margin-bottom:30px; border-left:4px solid var(--accent); background:var(--bg-body);">
    <div style="font-size:0.85rem; color:var(--dim); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;"><i class="fas fa-comment-dots"></i> Feedback del TFG</div>
    <div style="font-size:1.05rem; font-style:italic;">"<?= Security::escapeHtml($resumen['obs_tfg']) ?>"</div>
</div>
<?php } ?>


<div class="grid-2col" style="align-items:start;">
    <!-- ═══════════════ MÓDULOS ═══════════════ -->
    <div class="panel" style="display:flex; flex-direction:column; gap:30px;">
        <div class="titulo-tarjeta" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-2); padding-bottom:15px; margin-bottom:0px;">
            <h3 style="margin:0;"><i class="fas fa-book-open" style="color:var(--accent);"></i> Calificaciones por Módulo</h3>
            <span class="badge badge-normal" style="font-size:0.75rem;"><i class="fas fa-info-circle"></i> 75% Exámenes + 25% Retos</span>
        </div>
        
        <?php
        $modulosPorAnio = [1 => [], 2 => []];
        if (!empty($resumen['detalles_modulos'])) {
            foreach ($resumen['detalles_modulos'] as $modulo) {
                $anio = (int)($modulo['cursoAnio'] ?? 1);
                $modulosPorAnio[$anio][] = $modulo;
            }
        }
        
        foreach ([1, 2] as $anio) {
            $modulosAnio = $modulosPorAnio[$anio];
            if (!empty($modulosAnio) || $anio === 1) { // Mostrar al menos 1º año aunque esté vacío
        ?>
        <div>
            <h4 style="margin: 0 0 10px 0; color: var(--text-color); border-left: 3px solid var(--accent); padding-left: 10px;">Módulos de <?= $anio ?>º Año</h4>
            <div class="contenedor-tabla">
                <table class="tabla-datos">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <th style="text-align:center;">Final</th>
                            <th style="text-align:center;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($modulosAnio)) { ?>
                            <?php foreach ($modulosAnio as $modulo) {
                                $estado = $modulo['estado'];
                                $claseEstado = $estado === 'Aprobado' ? 'badge-exito' : ($estado === 'Suspenso' ? 'badge-error' : 'badge-alerta');
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight:600; color:var(--text-color);"><?= Security::escapeHtml($modulo['nombreModulo']) ?></div>
                                    <div style="font-size:0.8rem; color:var(--dim); margin-top:3px;">
                                        Exámenes: <?= Security::escapeHtml($modulo['media_notas']) ?> | Retos: <?= Security::escapeHtml($modulo['media_retos']) ?>
                                    </div>
                                </td>
                                <td style="text-align:center; font-weight:800; font-size:1.15rem; color:var(--text-color);">
                                    <?= Security::escapeHtml($modulo['nota_final']) ?>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge <?= Security::escapeHtml($claseEstado) ?>"><?= Security::escapeHtml(strtoupper($estado)) ?></span>
                                </td>
                            </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" style="text-align:center; padding:20px;">
                                    <div class="texto-suave">Aún no hay calificaciones para <?= $anio ?>º año.</div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } } ?>
    </div>

    <!-- ═══════════════ RETOS ═══════════════ -->
    <div class="panel">
        <div class="titulo-tarjeta" style="border-bottom:1px solid var(--border-2); padding-bottom:15px; margin-bottom:15px;">
            <h3 style="margin:0;"><i class="fas fa-tasks" style="color:var(--accent);"></i> Calificaciones de Retos</h3>
        </div>
        
        <div class="contenedor-tabla">
            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th>Reto</th>
                        <th>Fechas</th>
                        <th style="text-align:center;">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($retosNotas)) { ?>
                        <?php foreach ($retosNotas as $reto) {
                            $nota = (float)$reto['nota'];
                            $claseNota = $nota >= 5 ? 'texto-verde' : 'texto-rojo';
                        ?>
                        <tr>
                            <td style="font-weight:600; color:var(--text-color);">
                                <?= Security::escapeHtml($reto['nombreReto']) ?>
                            </td>
                            <td style="font-size:0.85rem; color:var(--dim);">
                                <?= !empty($reto['fechaInicio']) ? date('d/m/y', strtotime($reto['fechaInicio'])) : '—' ?>
                                <br>
                                <?= !empty($reto['fechaFin']) ? date('d/m/y', strtotime($reto['fechaFin'])) : '—' ?>
                            </td>
                            <td style="text-align:center; font-weight:800; font-size:1.15rem;" class="<?= Security::escapeHtml($claseNota) ?>">
                                <?= Security::escapeHtml($reto['nota']) ?>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding:40px 20px;">
                                <div style="font-size:3rem; color:var(--border); margin-bottom:15px;"><i class="fas fa-check-square"></i></div>
                                <div class="texto-suave">No tienes calificaciones en retos por el momento.</div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


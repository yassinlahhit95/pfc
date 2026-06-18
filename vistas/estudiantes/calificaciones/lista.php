<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$resumen      = obtenerResultadosFinalesEstudiante($idEstudiante);
$retosNotas   = listarCalificacionesRetoPorEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MIS CALIFICACIONES";
$seccionActual   = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS CALIFICACIONES</h1>
    <p class="subtitulo"><?= Security::escapeHtml($resumen['nombreCiclo'] ?? '') ?></p>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<!-- ═══════════════ MÓDULOS ═══════════════ -->
<div class="panel">
    <div class="titulo-tarjeta">
        <h3>MÓDULOS <span class="texto-suave" style="font-size:.8rem;font-weight:400;">(75% exámenes + 25% retos)</span></h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>Media Exámenes (75%)</th>
                    <th>Media Retos (25%)</th>
                    <th>Nota Final</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($resumen['detalles_modulos'])) { ?>
                    <?php foreach ($resumen['detalles_modulos'] as $d) {
                        $est = $d['estado'];
                        $cls = $est === 'Aprobado' ? 'badge-exito' : ($est === 'Suspenso' ? 'badge-error' : 'badge-alerta');
                    ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($d['nombreModulo']) ?></b></td>
                        <td><?= Security::escapeHtml($d['media_notas']) ?></td>
                        <td><?= Security::escapeHtml($d['media_retos']) ?></td>
                        <td class="texto-negrita"><?= Security::escapeHtml($d['nota_final']) ?></td>
                        <td><span class="badge <?= Security::escapeHtml($cls) ?>"><?= Security::escapeHtml(strtoupper($est)) ?></span></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="5" class="vacio">No hay calificaciones registradas.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══════════════ RETOS ═══════════════ -->
<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta">
        <h3>RETOS</h3>
    </div>
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Reto</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Nota</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($retosNotas)) { ?>
                    <?php foreach ($retosNotas as $r) {
                        $nota = (float)$r['nota'];
                        $cls  = $nota >= 5 ? 'texto-verde' : 'texto-rojo';
                    ?>
                    <tr>
                        <td><b><?= Security::escapeHtml($r['nombreReto']) ?></b></td>
                        <td><?= Security::escapeHtml($r['fechaInicio']) ?></td>
                        <td><?= Security::escapeHtml($r['fechaFin']) ?></td>
                        <td class="texto-negrita <?= Security::escapeHtml($cls) ?>" style="font-size:1.05em;"><?= Security::escapeHtml($r['nota']) ?></td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="4" class="vacio">Aún no tienes calificaciones en retos.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ═══════════════ TFG ═══════════════ -->
<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta">
        <h3>TFG — TRABAJO DE FIN DE GRADO</h3>
    </div>
    <div style="padding:16px 20px;display:flex;align-items:center;gap:24px;">
        <?php
        $notaTfg = $resumen['nota_tfg'] ?? null;
        if ($notaTfg !== null) {
            $tfgCls = (float)$notaTfg >= 5 ? 'badge-exito' : 'badge-error';
            $tfgEst = (float)$notaTfg >= 5 ? 'APROBADO' : 'SUSPENSO';
        } else {
            $tfgCls = 'badge-alerta';
            $tfgEst = 'PENDIENTE';
        }
        ?>
        <div>
            <p class="texto-suave" style="margin:0 0 4px;">Nota obtenida:</p>
            <span style="font-size:2rem;font-weight:700;color:var(--color-primario);">
                <?= Security::escapeHtml($notaTfg !== null ? $notaTfg : '—') ?>
            </span>
        </div>
        <div>
            <p class="texto-suave" style="margin:0 0 4px;">Estado:</p>
            <span class="badge <?= Security::escapeHtml($tfgCls) ?>" style="font-size:1rem;">
                <?= Security::escapeHtml($tfgEst) ?>
            </span>
        </div>
        <?php if (!empty($resumen['obs_tfg'])) { ?>
        <div style="flex:1;">
            <p class="texto-suave" style="margin:0 0 4px;">Observaciones:</p>
            <p style="margin:0;"><?= Security::escapeHtml($resumen['obs_tfg']) ?></p>
        </div>
        <?php } ?>
    </div>
</div>

<!-- ═══════════════ RESUMEN GLOBAL ═══════════════ -->
<div class="panel" style="margin-top:20px;">
    <div class="titulo-tarjeta"><h3>RESUMEN GLOBAL</h3></div>
    <div class="caja espacio-entre-elementos alinear-centro" style="padding:16px 20px;">
        <div>
            <p class="texto-suave">Promedio General:</p>
            <h2 class="color-primario"><?= Security::escapeHtml((string)$resumen['promedio_global']) ?></h2>
        </div>
        <div style="text-align:right;">
            <p class="texto-suave">Estado Académico:</p>
            <?php
            $gCls = $resumen['estado_global'] === 'APROBADO' ? 'badge-exito' : ($resumen['estado_global'] === 'SUSPENSO' ? 'badge-error' : 'badge-alerta');
            ?>
            <span class="badge <?= Security::escapeHtml($gCls) ?>" style="font-size:1rem;">
                <?= Security::escapeHtml($resumen['estado_global']) ?>
            </span>
        </div>
    </div>
    <div class="tarjeta-gris-suave" style="margin:0 16px 16px;">
        <p><b>Nota:</b> La nota final de cada módulo se calcula como 75% media de exámenes + 25% media de retos.</p>
        <p><b>Estados:</b> <span class="texto-verde">Aprobado (≥ 5.0)</span>, <span class="texto-rojo">Suspenso (&lt; 5.0)</span>, <span class="texto-gris">Pendiente (sin notas)</span>.</p>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

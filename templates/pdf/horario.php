<?php
// Required vars: $cfg, $ciclo, $franjas, $dias, $celdas

$_logo1  = logoParaPdf($cfg['logoGobierno1'] ?? '');
$_logo2  = logoParaPdf($cfg['logoCentro'] ?: ($cfg['logoGobierno2'] ?? ''));
$numDias = count($dias);

// ── Color palette — dark enough for white text readability ──────────────
$paleta     = ['#1e40af','#0e7490','#065f46','#92400e','#9f1239','#5b21b6','#9d174d','#075985','#166534','#7c2d12'];
$paletaDark = ['#1e3a8a','#0c5f7a','#044936','#78350f','#881337','#4c1d95','#831843','#0c4a6e','#14532d','#6c2302'];

$colorMap = [];
$badgeMap = [];
foreach ($celdas as $celda) {
    $id = (int)$celda['idModulo'];
    if (!isset($colorMap[$id])) {
        $colorMap[$id] = $paleta[$id % count($paleta)];
        $badgeMap[$id] = $paletaDark[$id % count($paletaDark)];
    }
}

// ── Legend: unique modules present in the schedule ──────────────────────
$modulosEnHorario = [];
foreach ($celdas as $celda) {
    $id = (int)$celda['idModulo'];
    if (!isset($modulosEnHorario[$id])) {
        $modulosEnHorario[$id] = $celda['nombreModulo'] ?? ('Módulo #' . $id);
    }
}
$legendRows = array_chunk(array_keys($modulosEnHorario), 4, false);

// ── Center info strings ─────────────────────────────────────────────────
$centroDireccion = trim(
    ($cfg['direccionCentro'] ?? '') .
    (!empty($cfg['ciudadCentro']) ? ', ' . $cfg['ciudadCentro'] : '') .
    (!empty($cfg['cpCentro'])     ? ' — ' . $cfg['cpCentro']   : '')
);
$centroContacto = [];
if (!empty($cfg['telefonoCentro'])) $centroContacto[] = 'Tel. ' . $cfg['telefonoCentro'];
if (!empty($cfg['emailCentro']))    $centroContacto[] = $cfg['emailCentro'];
$contactoStr = implode('  ·  ', $centroContacto);
?>
<style>
@page {
    header: page-header;
    footer: page-footer;
    margin-top:    56mm;
    margin-bottom: 20mm;
    margin-header: 4mm;
    margin-footer: 3mm;
}
body { font-family: dejavusans, sans-serif; color: #1e293b; margin: 0; padding: 0; }

/* ── Schedule table ─────────────────────────────────────── */
.tabla-horario {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-top: 4px;
}
.tabla-horario thead th {
    background: #0f172a;
    color: #e2e8f0;
    padding: 9px 4px;
    font-size: 8pt;
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border: 1px solid #1e293b;
}
.th-hora { width: 56px; }
.tabla-horario tbody td {
    border: 1px solid #e2e8f0;
    padding: 3px;
    height: 50px;
    vertical-align: middle;
    text-align: center;
    font-size: 7.5pt;
    background: #ffffff;
}

/* ── Time column ─────────────────────────────────────────── */
.td-hora {
    background: #f8fafc;
    border-right: 2px solid #cbd5e1 !important;
    vertical-align: middle;
    text-align: center;
}
.td-hora-start { font-size: 8.5pt; font-weight: bold; color: #1e293b; }
.td-hora-end   { font-size: 6pt;   color: #94a3b8; margin-top: 2px; }

/* ── Break row ───────────────────────────────────────────── */
.tr-recreo td { height: 26px !important; }
.td-recreo-hora {
    background: #fefce8;
    font-size: 6pt; color: #92400e;
    border-right: 2px solid #fcd34d !important;
    border: 1px solid #fde68a;
}
.td-recreo-body {
    background: #fefce8;
    border: 1px solid #fde68a !important;
    color: #92400e;
    font-weight: bold;
    font-size: 7.5pt;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    text-align: center;
}

/* ── Module cell ─────────────────────────────────────────── */
.celda-wrap {
    border-radius: 5px;
    padding: 5px 5px 4px;
    display: block;
    height: 44px;
    overflow: hidden;
    text-align: left;
}
.celda-modulo {
    font-weight: bold;
    font-size: 6.5pt;
    color: #ffffff;
    line-height: 1.3;
    margin-bottom: 2px;
}
.celda-prof {
    font-size: 5.5pt;
    color: #e2e8f0;
    line-height: 1.2;
}
.celda-badge {
    display: inline-block;
    border-radius: 2px;
    color: #ffffff;
    font-size: 5pt;
    font-weight: bold;
    padding: 1px 4px;
    margin-top: 3px;
}

/* ── Legend ──────────────────────────────────────────────── */
.leyenda-section { margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 6px; }
.leyenda-titulo  { font-size: 6.5pt; font-weight: bold; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 5px; }
.leyenda-dot     { display: inline-block; border-radius: 3px; padding: 2px 6px; font-size: 6pt; color: #fff; font-weight: bold; margin-right: 2px; vertical-align: middle; }
.leyenda-nombre  { font-size: 6.5pt; color: #334155; vertical-align: middle; }

/* ── Footer ──────────────────────────────────────────────── */
.footer-bar {
    border-top: 1px solid #e2e8f0;
    padding-top: 5px;
    font-size: 6.5pt;
    color: #94a3b8;
    text-align: center;
}
</style>

<!-- ══════════════════════════════════════════════════════════
     PAGE HEADER
     ══════════════════════════════════════════════════════════ -->
<htmlpageheader name="page-header">

    <!-- ── Top accent strip ── -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:0;">
        <tr>
            <td style="background:#1e3a6e; height:7px; font-size:1pt; padding:0; line-height:1;">&#160;</td>
        </tr>
    </table>

    <!-- ── Logos + Center identity ── -->
    <table style="width:100%; border-collapse:collapse; padding:7px 0 5px;">
        <tr>
            <!-- Logo izquierdo (institución/gobierno) -->
            <td style="width:14%; vertical-align:middle; padding:0 6px 0 0; text-align:left;">
                <?php if ($_logo1): ?>
                <img src="<?= $_logo1 ?>" style="max-height:42px; max-width:88px;">
                <?php endif; ?>
            </td>

            <!-- Nombre y datos del centro -->
            <td style="vertical-align:middle; text-align:center; padding:0 8px;">
                <div style="font-size:13pt; font-weight:bold; color:#1e3a6e; letter-spacing:0.02em; line-height:1.2;">
                    <?= htmlspecialchars($cfg['nombreCentro'] ?? 'Centro de Formación Profesional', ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php if ($centroDireccion): ?>
                <div style="font-size:7pt; color:#64748b; margin-top:3px;">
                    <?= htmlspecialchars($centroDireccion, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>
                <?php if ($contactoStr): ?>
                <div style="font-size:6.5pt; color:#94a3b8; margin-top:1px;">
                    <?= htmlspecialchars($contactoStr, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>
            </td>

            <!-- Logo derecho (centro / gobierno 2) -->
            <td style="width:14%; vertical-align:middle; padding:0 0 0 6px; text-align:right;">
                <?php if ($_logo2): ?>
                <img src="<?= $_logo2 ?>" style="max-height:42px; max-width:88px;">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- ── Info band ── -->
    <table style="width:100%; border-collapse:collapse; background:#1e3a6e; border-radius:4px;">
        <tr>
            <!-- Title + ciclo -->
            <td style="padding:6px 12px; vertical-align:middle;">
                <span style="font-size:7pt; color:#93c5fd; font-weight:bold; letter-spacing:0.09em; text-transform:uppercase;">
                    Cuadro Horario Semanal
                </span>
                <span style="color:#e2e8f0; font-size:9pt; font-weight:bold; margin-left:8px;">
                    <?= htmlspecialchars($ciclo['nombreCiclo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span style="background:#ffffff22; border-radius:3px; padding:1px 7px; font-size:7.5pt; color:#bfdbfe; font-weight:bold; margin-left:6px;">
                    <?= htmlspecialchars($ciclo['abreviaturaCiclo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php if (!empty($cfg['codigoCentro'])): ?>
                <span style="color:#64748b; font-size:7pt; margin-left:10px;">
                    Código: <?= htmlspecialchars($cfg['codigoCentro'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php endif; ?>
            </td>
            <!-- Academic year + date -->
            <td style="text-align:right; padding:6px 12px; white-space:nowrap; width:1%; vertical-align:middle;">
                <span style="color:#93c5fd; font-size:7.5pt; font-weight:bold;">
                    Curso <?= htmlspecialchars($cfg['cursoEscolar'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </span>
                <span style="color:#4b6080; font-size:6.5pt; margin-left:8px;">
                    <?= date('d/m/Y') ?>
                </span>
            </td>
        </tr>
    </table>

</htmlpageheader>

<!-- ══════════════════════════════════════════════════════════
     PAGE FOOTER
     ══════════════════════════════════════════════════════════ -->
<htmlpagefooter name="page-footer">
    <div class="footer-bar">
        <?= htmlspecialchars($cfg['nombreCentro'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        &nbsp;&mdash;&nbsp;
        <?= htmlspecialchars($ciclo['nombreCiclo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        (<?= htmlspecialchars($ciclo['abreviaturaCiclo'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
        &nbsp;&mdash;&nbsp;
        Curso <?= htmlspecialchars($cfg['cursoEscolar'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        &nbsp;&mdash;&nbsp;
        AulaPro
        &nbsp;&nbsp;|&nbsp;&nbsp;
        P&aacute;gina {PAGENO} de {nb}
    </div>
</htmlpagefooter>

<!-- ══════════════════════════════════════════════════════════
     SCHEDULE TABLE
     ══════════════════════════════════════════════════════════ -->
<table class="tabla-horario">
    <thead>
        <tr>
            <th class="th-hora">Hora</th>
            <?php foreach ($dias as $dia): ?>
            <th><?= htmlspecialchars($dia, ENT_QUOTES, 'UTF-8') ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($franjas as $f):
            $inicio = substr($f['inicio'], 0, 5);
            $fin    = substr($f['fin'],    0, 5);
        ?>

        <?php if ($f['recreo']): ?>
        <!-- Break row -->
        <tr class="tr-recreo">
            <td class="td-hora td-recreo-hora">
                <div class="td-hora-start"><?= $inicio ?></div>
                <div class="td-hora-end"><?= $fin ?></div>
            </td>
            <td colspan="<?= $numDias ?>" class="td-recreo-body">
                &#9749;&nbsp; DESCANSO &nbsp;&mdash;&nbsp; <?= $inicio ?> &ndash; <?= $fin ?>
            </td>
        </tr>

        <?php else: ?>
        <!-- Regular row -->
        <tr>
            <td class="td-hora">
                <div class="td-hora-start"><?= $inicio ?></div>
                <div class="td-hora-end"><?= $fin ?></div>
            </td>
            <?php foreach ($dias as $dia):
                $clave = $dia . '|' . $inicio;
                $celda = $celdas[$clave] ?? null;
                $color = $celda ? ($colorMap[(int)$celda['idModulo']] ?? '#475569') : null;
                $badge = $celda ? ($badgeMap[(int)$celda['idModulo']] ?? '#334155') : null;
                $aula  = !empty($celda['codigoAula']) ? $celda['codigoAula'] : (!empty($celda['nombreAula']) ? $celda['nombreAula'] : '');
            ?>
            <td>
                <?php if ($celda): ?>
                <div class="celda-wrap" style="background:<?= $color ?>;">
                    <div class="celda-modulo">
                        <?= htmlspecialchars(mb_strimwidth($celda['nombreModulo'] ?? '', 0, 42, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="celda-prof">
                        <?= htmlspecialchars(mb_strimwidth($celda['nombreProfesor'] ?? '', 0, 34, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php if ($aula): ?>
                    <span class="celda-badge" style="background:<?= $badge ?>;">
                        <?= htmlspecialchars(mb_strimwidth($aula, 0, 16, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div style="height:44px;"></div>
                <?php endif; ?>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php endif; ?>

        <?php endforeach; ?>
    </tbody>
</table>

<!-- ══════════════════════════════════════════════════════════
     MODULE LEGEND
     ══════════════════════════════════════════════════════════ -->
<?php if (!empty($modulosEnHorario)): ?>
<div class="leyenda-section">
    <div class="leyenda-titulo">Leyenda de módulos</div>
    <table style="width:100%; border-collapse:collapse;">
        <?php foreach ($legendRows as $fila): ?>
        <tr>
            <?php foreach ($fila as $id): ?>
            <td style="padding:2px 6px 2px 0; width:25%; vertical-align:middle;">
                <span class="leyenda-dot" style="background:<?= $colorMap[$id] ?>;"> &#160; </span>
                <span class="leyenda-nombre"><?= htmlspecialchars($modulosEnHorario[$id], ENT_QUOTES, 'UTF-8') ?></span>
            </td>
            <?php endforeach; ?>
            <!-- Pad empty cols -->
            <?php for ($p = count($fila); $p < 4; $p++): ?>
            <td style="width:25%;"></td>
            <?php endfor; ?>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

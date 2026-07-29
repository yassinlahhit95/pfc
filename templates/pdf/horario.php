<?php
// PDF: Cuadro Horario (Weekly Schedule)
// Required vars: $cfg, $ciclo, $franjas, $dias, $celdas

include __DIR__ . '/_styles.php';
include __DIR__ . '/_helpers.php';

$_logo1  = logoParaPdf($cfg['logoGobierno1'] ?? '');
$_logo2  = logoParaPdf($cfg['logoCentro'] ?: ($cfg['logoGobierno2'] ?? ''));
$_numDias = count($dias);

// Color palette for modules
$_paleta     = ['#1e40af','#0e7490','#065f46','#92400e','#9f1239','#5b21b6','#9d174d','#075985','#166534','#7c2d12'];
$_paletaDark = ['#1e3a8a','#0c5f7a','#044936','#78350f','#881337','#4c1d95','#831843','#0c4a6e','#14532d','#6c2302'];

$_colorMap = [];
$_badgeMap = [];
foreach ($celdas as $_celda) {
    $_id = (int)$_celda['idModulo'];
    if (!isset($_colorMap[$_id])) {
        $_colorMap[$_id] = $_paleta[$_id % count($_paleta)];
        $_badgeMap[$_id] = $_paletaDark[$_id % count($_paletaDark)];
    }
}

// Build module legend
$_modulosEnHorario = [];
foreach ($celdas as $_celda) {
    $_id = (int)$_celda['idModulo'];
    if (!isset($_modulosEnHorario[$_id])) {
        $_modulosEnHorario[$_id] = $_celda['nombreModulo'] ?? ('Módulo #' . $_id);
    }
}
$_legendRows = array_chunk(array_keys($_modulosEnHorario), 4, false);

// Format center info
$_centroDireccion = trim(
    ($cfg['direccionCentro'] ?? '') .
    (!empty($cfg['ciudadCentro']) ? ', ' . $cfg['ciudadCentro'] : '') .
    (!empty($cfg['cpCentro'])     ? ' — ' . $cfg['cpCentro']   : '')
);
$_centroContacto = [];
if (!empty($cfg['telefonoCentro'])) $_centroContacto[] = 'Tel. ' . $cfg['telefonoCentro'];
if (!empty($cfg['emailCentro']))    $_centroContacto[] = $cfg['emailCentro'];
$_contactoStr = implode('  ·  ', $_centroContacto);
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

/* Schedule table with fixed layout */
.pdf-schedule {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    margin-top: var(--pdf-space-2);
}
.pdf-schedule thead th {
    background: #0f172a;
    color: #e2e8f0;
    padding: var(--pdf-space-3) var(--pdf-space-2);
    font-size: var(--pdf-text-sm);
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    border: 1px solid #1e293b;
}
.pdf-schedule-time {
    width: 56px;
}
.pdf-schedule tbody td {
    border: 1px solid var(--pdf-border);
    padding: var(--pdf-space-1);
    height: 50px;
    vertical-align: middle;
    text-align: center;
    font-size: var(--pdf-text-xs);
    background: #ffffff;
}

/* Time column */
.pdf-time-cell {
    background: var(--pdf-bg-light);
    border-right: 2px solid #cbd5e1 !important;
    vertical-align: middle;
    text-align: center;
}
.pdf-time-start { font-size: var(--pdf-text-sm); font-weight: bold; color: var(--pdf-text); }
.pdf-time-end   { font-size: 6pt; color: var(--pdf-text-muted); margin-top: var(--pdf-space-1); }

/* Break row */
.pdf-break-row td { height: 26px !important; }
.pdf-break-cell {
    background: #fefce8;
    color: #a16207;
    font-weight: bold;
    font-size: var(--pdf-text-base);
}

/* Module cell */
.pdf-class-cell {
    position: relative;
    overflow: hidden;
    background-size: cover;
}
.pdf-class-module {
    font-weight: bold;
    color: #ffffff;
    font-size: 8pt;
    line-height: 1.3;
}
.pdf-class-prof {
    font-size: 6.5pt;
    color: #e2e8f0;
    margin-top: 2px;
}
.pdf-class-badge {
    display: inline-block;
    background-color: rgba(0, 0, 0, 0.4);
    color: #ffffff;
    font-size: 6pt;
    padding: 2px 6px;
    border-radius: 3px;
    margin-top: 2px;
}

/* Legend section */
.pdf-legend-title {
    font-size: var(--pdf-text-base);
    font-weight: bold;
    color: var(--pdf-primary);
    margin-top: var(--pdf-space-9);
    margin-bottom: var(--pdf-space-5);
    text-transform: uppercase;
}
.pdf-legend-table {
    width: 100%;
    border-collapse: collapse;
    font-size: var(--pdf-text-xs);
}
.pdf-legend-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: var(--pdf-space-2);
    vertical-align: middle;
}
</style>

<htmlpageheader name="page-header">
    <!-- Accent strip -->
    <table style="width:100%; border-collapse:collapse; margin-bottom:0;">
        <tr>
            <td style="background:var(--pdf-primary); height:7px; font-size:1pt; padding:0; line-height:1;"></td>
        </tr>
    </table>

    <!-- Logos + Center identity -->
    <table style="width:100%; border-collapse:collapse; padding:var(--pdf-space-3) 0;">
        <tr>
            <td style="width:14%; vertical-align:middle; padding:0 var(--pdf-space-3) 0 0; text-align:left;">
                <?php if ($_logo1): ?>
                <img src="<?= $_logo1 ?>" alt="Logotipo del gobierno" style="max-height:42px; max-width:88px;">
                <?php endif; ?>
            </td>
            <td style="vertical-align:middle; text-align:center; padding:0 var(--pdf-space-4);">
                <div style="font-size:var(--pdf-text-2xl); font-weight:bold; color:var(--pdf-primary); letter-spacing:0.02em; line-height:1.2;">
                    <?= pdfAssertField($cfg['nombreCentro'] ?? null, 'cfg.nombreCentro') ?>
                </div>
                <?php if ($_centroDireccion): ?>
                <div style="font-size:var(--pdf-text-sm); color:var(--pdf-text-light); margin-top:var(--pdf-space-1);">
                    <?= htmlspecialchars($_centroDireccion, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>
                <?php if ($_contactoStr): ?>
                <div style="font-size:var(--pdf-text-xs); color:var(--pdf-text-muted); margin-top:var(--pdf-space-1);">
                    <?= htmlspecialchars($_contactoStr, ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>
            </td>
            <td style="width:14%; vertical-align:middle; padding:0 0 0 var(--pdf-space-3); text-align:right;">
                <?php if ($_logo2): ?>
                <img src="<?= $_logo2 ?>" alt="Logotipo del centro" style="max-height:42px; max-width:88px;">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Info band -->
    <table style="width:100%; border-collapse:collapse; background:var(--pdf-primary); border-radius:4px;">
        <tr>
            <td style="padding:var(--pdf-space-4) var(--pdf-space-6); vertical-align:middle;">
                <span style="font-size:var(--pdf-text-xs); color:#93c5fd; font-weight:bold; letter-spacing:0.09em; text-transform:uppercase;">Cuadro Horario Semanal</span>
                <span style="color:#e2e8f0; font-size:var(--pdf-text-base); font-weight:bold; margin-left:var(--pdf-space-4);">
                    <?= pdfAssertField($ciclo['nombreCiclo'] ?? null, 'ciclo.nombreCiclo') ?>
                </span>
                <span style="background:#ffffff22; border-radius:3px; padding:var(--pdf-space-1) var(--pdf-space-3); font-size:var(--pdf-text-xs); color:#bfdbfe; font-weight:bold; margin-left:var(--pdf-space-3);">
                    <?= pdfAssertField($ciclo['abreviaturaCiclo'] ?? null, 'ciclo.abreviaturaCiclo') ?>
                </span>
                <?php if (!empty($cfg['codigoCentro'])): ?>
                <span style="color:var(--pdf-text-light); font-size:var(--pdf-text-xs); margin-left:var(--pdf-space-5);">
                    Código: <?= pdfAssertField($cfg['codigoCentro'] ?? null, 'cfg.codigoCentro') ?>
                </span>
                <?php endif; ?>
            </td>
            <td style="text-align:right; padding:var(--pdf-space-4) var(--pdf-space-6); white-space:nowrap; width:1%; vertical-align:middle;">
                <span style="color:#93c5fd; font-size:var(--pdf-text-xs); font-weight:bold;">Curso <?= pdfAssertField($cfg['cursoEscolar'] ?? null, 'cfg.cursoEscolar') ?></span>
                <span style="color:#4b6080; font-size:var(--pdf-text-xs); margin-left:var(--pdf-space-4);"><?= date('d/m/Y') ?></span>
            </td>
        </tr>
    </table>
</htmlpageheader>

<?php include __DIR__ . '/_footer.php'; $cicloInfo = pdfAssertField($ciclo['nombreCiclo'] ?? null, 'ciclo.nombreCiclo') . ' (' . pdfAssertField($ciclo['abreviaturaCiclo'] ?? null, 'ciclo.abreviaturaCiclo') . ')'; ?>

<!-- Schedule grid -->
<table class="pdf-schedule">
    <thead>
        <tr>
            <th class="pdf-schedule-time">Hora</th>
            <?php foreach ($dias as $_dia): ?>
            <th><?= pdfAssertField($_dia, 'dia') ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($franjas as $_f):
            $_inicio = substr($_f['inicio'], 0, 5);
            $_fin    = substr($_f['fin'],    0, 5);
        ?>

        <?php if ($_f['recreo']): ?>
        <!-- Break row -->
        <tr class="pdf-break-row">
            <td class="pdf-time-cell">
                <div class="pdf-time-start"><?= htmlspecialchars($_inicio, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="pdf-time-end"><?= htmlspecialchars($_fin, ENT_QUOTES, 'UTF-8') ?></div>
            </td>
            <td colspan="<?= $_numDias ?>" class="pdf-break-cell">☕ DESCANSO &nbsp;&mdash;&nbsp; <?= htmlspecialchars($_inicio, ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($_fin, ENT_QUOTES, 'UTF-8') ?></td>
        </tr>

        <?php else: ?>
        <!-- Regular class row -->
        <tr>
            <td class="pdf-time-cell">
                <div class="pdf-time-start"><?= htmlspecialchars($_inicio, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="pdf-time-end"><?= htmlspecialchars($_fin, ENT_QUOTES, 'UTF-8') ?></div>
            </td>
            <?php foreach ($dias as $_dia):
                $_clave = $_dia . '|' . $_inicio;
                $_celda = $celdas[$_clave] ?? null;
                $_color = $_celda ? ($_colorMap[(int)$_celda['idModulo']] ?? '#475569') : null;
                $_badge = $_celda ? ($_badgeMap[(int)$_celda['idModulo']] ?? '#334155') : null;
                $_aula  = !empty($_celda['codigoAula']) ? $_celda['codigoAula'] : (!empty($_celda['nombreAula']) ? $_celda['nombreAula'] : '');
            ?>
            <td>
                <?php if ($_celda): ?>
                <div class="pdf-class-cell" style="background:<?= htmlspecialchars($_color, ENT_QUOTES, 'UTF-8') ?>;">
                    <div class="pdf-class-module">
                        <?= pdfTruncate($_celda['nombreModulo'] ?? '', 42) ?>
                    </div>
                    <div class="pdf-class-prof">
                        <?= pdfTruncate($_celda['nombreProfesor'] ?? '', 34) ?>
                    </div>
                    <?php if ($_aula): ?>
                    <span class="pdf-class-badge" style="background:<?= htmlspecialchars($_badge, ENT_QUOTES, 'UTF-8') ?>;">
                        <?= pdfTruncate($_aula, 16) ?>
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

<!-- Module legend -->
<?php if (!empty($_modulosEnHorario)): ?>
<div class="pdf-legend-title">Leyenda de módulos</div>
<table class="pdf-legend-table">
    <?php foreach ($_legendRows as $_fila): ?>
    <tr>
        <?php foreach ($_fila as $_id): ?>
        <td style="padding:var(--pdf-space-2) var(--pdf-space-3) var(--pdf-space-2) 0; width:25%; vertical-align:middle;">
            <span class="pdf-legend-dot" style="background:<?= htmlspecialchars($_colorMap[$_id], ENT_QUOTES, 'UTF-8') ?>;"></span>
            <span><?= pdfAssertField($_modulosEnHorario[$_id] ?? null, 'modulo.nombre') ?></span>
        </td>
        <?php endforeach; ?>
        <!-- Pad empty cols -->
        <?php for ($_p = count($_fila); $_p < 4; $_p++): ?>
        <td style="width:25%;"></td>
        <?php endfor; ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

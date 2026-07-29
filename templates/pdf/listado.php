<?php
// PDF: Listado de Alumnado (Student Roster)
// Required vars: $cfg, $ciclo, $estudiantes

include __DIR__ . '/_styles.php';
include __DIR__ . '/_helpers.php';
?>

<htmlpageheader name="page-header">
    <table style="width:100%; border-collapse:collapse; border-bottom: 2px solid var(--pdf-primary); padding-bottom: var(--pdf-space-8);">
        <tr>
            <td width="20%"><img src="<?= logoParaPdf($cfg['logoGobierno1']) ?>" alt="Logotipo del gobierno" style="max-height: 50px;"></td>
            <td width="60%" align="center">
                <div style="font-size: var(--pdf-text-2xl); font-weight: bold; color: var(--pdf-primary);"><?= pdfAssertField($cfg['nombreCentro'] ?? null, 'cfg.nombreCentro') ?></div>
                <div style="font-size: var(--pdf-text-sm); color: var(--pdf-text-light);"><?= pdfAssertField($cfg['direccionCentro'] ?? null, 'cfg.direccionCentro') ?></div>
            </td>
            <td width="20%" align="right"><img src="<?= logoParaPdf($cfg['logoCentro'] ?: ($cfg['logoGobierno2'] ?? '')) ?>" alt="Logotipo del centro" style="max-height: 50px;"></td>
        </tr>
    </table>
</htmlpageheader>

<?php include __DIR__ . '/_footer.php'; $cicloInfo = pdfAssertField($ciclo['nombreCiclo'] ?? null, 'ciclo.nombreCiclo'); ?>

<div style="text-align: center; margin-bottom: var(--pdf-space-9);">
    <h1 class="pdf-title">Listado de Alumnado</h1>
    <div class="pdf-subtitle">
        <?= $cicloInfo ?>
        — Curso <?= pdfAssertField($cfg['cursoEscolar'] ?? null, 'cfg.cursoEscolar') ?>
    </div>
</div>

<div style="text-align:right; margin-bottom: var(--pdf-space-5);">
    <span class="pdf-badge pending"><?= count($estudiantes) ?> estudiantes</span>
</div>

<table class="pdf-table">
    <thead>
        <tr class="pdf-table-header">
            <th width="5%">#</th>
            <th width="35%">Nombre y Apellidos</th>
            <th width="12%">DNI/NIE</th>
            <th width="28%">Correo Electrónico</th>
            <th width="12%">Teléfono</th>
            <th width="8%">Ciclo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($estudiantes as $_i => $_est): ?>
        <tr class="<?= $_i % 2 == 0 ? 'pdf-row-even' : 'pdf-row-odd' ?>">
            <td style="text-align:center; font-size:var(--pdf-text-xs); color:var(--pdf-text-muted);"><?= $_i + 1 ?></td>
            <td style="font-weight:500;"><?= pdfAssertField($_est['nombreEstudiante'] ?? null, 'estudiante.nombreEstudiante') ?></td>
            <td><?= pdfAssertField($_est['dniEstudiante'] ?? null, 'estudiante.dniEstudiante') ?></td>
            <td style="color:var(--pdf-info);"><?= pdfAssertField($_est['emailEstudiante'] ?? null, 'estudiante.emailEstudiante') ?></td>
            <td><?= pdfAssertField($_est['telefonoEstudiante'] ?? null, 'estudiante.telefonoEstudiante') ?></td>
            <td style="font-weight:bold; color:var(--pdf-text-muted); font-size:var(--pdf-text-xs);"><?= pdfAssertField($_est['abreviaturaCiclo'] ?? null, 'estudiante.abreviaturaCiclo') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="pdf-signature-area">
    <tr>
        <td width="60%"></td>
        <td width="40%">
            <div class="pdf-signature-box">
                Fdo: <strong><?= pdfAssertField($cfg['nombreDirectorFirmante'] ?? null, 'cfg.nombreDirectorFirmante') ?></strong><br>
                Director/a del Centro
            </div>
        </td>
    </tr>
</table>

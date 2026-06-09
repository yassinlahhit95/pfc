<?php // Required vars: $cfg, $ciclo, $estudiantes ?>
<style>
    @page {
        header: page-header;
        footer: page-footer;
        margin-top: 35mm;
    }
    body { font-family: 'Roboto', sans-serif; color: #334155; }
    
    .titulo-doc { text-align:center; font-size:16pt; font-weight:700; color:#1e3a6e;
                  letter-spacing:.05em; margin: 0 0 5px; text-transform:uppercase; }
    .subtitulo-doc { text-align:center; font-size:10pt; color:#64748b; margin-bottom:20px; }
    
    .total-badge { background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569; font-size: 8pt;
                   padding: 4px 12px; border-radius: 6px; font-weight: bold; }

    .tabla-listado { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .tabla-listado th { background: #1e3a6e; color: #ffffff; padding: 10px 8px; font-size: 8.5pt; text-align: left; text-transform: uppercase; }
    .tabla-listado td { padding: 8px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; vertical-align: middle; }
    .tabla-listado tr:nth-child(even) { background: #f8fafc; }
    
    .header-table { width: 100%; border-bottom: 2px solid #1e3a6e; padding-bottom: 15px; }
</style>

<htmlpageheader name="page-header">
    <table class="header-table">
        <tr>
            <td width="20%"><img src="<?= logoParaPdf($cfg['logoGobierno1']) ?>" style="max-height: 50px;"></td>
            <td width="60%" align="center">
                <div style="font-size: 14pt; font-weight: bold; color: #1e3a6e;"><?= htmlspecialchars($cfg['nombreCentro']) ?></div>
                <div style="font-size: 8pt; color: #64748b;"><?= htmlspecialchars($cfg['direccionCentro']) ?></div>
            </td>
            <td width="20%" align="right"><img src="<?= logoParaPdf($cfg['logoCentro'] ?: $cfg['logoGobierno2']) ?>" style="max-height: 50px;"></td>
        </tr>
    </table>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div style="border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 8pt; color: #94a3b8; text-align: center;">
        Listado oficial de alumnado — Generado por AulaPro — Página {PAGENO} de {nb}
    </div>
</htmlpagefooter>

<div class="titulo-doc">Listado de Alumnado</div>
<div class="subtitulo-doc">
    <?= htmlspecialchars($ciclo['nombreCiclo'] ?? 'Todos los Ciclos') ?>
    — Curso <?= htmlspecialchars($cfg['cursoEscolar']) ?>
</div>

<div style="text-align:right; margin-bottom:10px;">
    <span class="total-badge"><?= count($estudiantes) ?> estudiantes</span>
</div>

<table class="tabla-listado">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="35%">Nombre y Apellidos</th>
            <th width="12%">DNI/NIE</th>
            <th width="28%">Correo Electrónico</th>
            <th width="12%">Teléfono</th>
            <th width="8%">Ciclo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($estudiantes as $i => $est): ?>
        <tr>
            <td style="color:#94a3b8; font-size:8pt; text-align:center;"><?= $i + 1 ?></td>
            <td style="font-weight:500;"><?= htmlspecialchars(mb_strtoupper($est['nombreEstudiante'], 'UTF-8')) ?></td>
            <td><?= htmlspecialchars($est['dniEstudiante'] ?? '—') ?></td>
            <td style="font-size:8.5pt; color:#2563eb;"><?= htmlspecialchars($est['emailEstudiante'] ?? '—') ?></td>
            <td><?= htmlspecialchars($est['telefonoEstudiante'] ?? '—') ?></td>
            <td style="font-size:7.5pt; font-weight:bold; color:#64748b;"><?= htmlspecialchars($est['abreviaturaCiclo'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div style="margin-top: 40px; width: 100%;">
    <table width="100%">
        <tr>
            <td width="60%"></td>
            <td width="40%" style="text-align: center; border-top: 1px solid #94a3b8; padding-top: 8px; font-size: 9pt;">
                Fdo: <strong><?= htmlspecialchars($cfg['nombreDirectorFirmante']) ?></strong><br>
                Director/a del Centro
            </td>
        </tr>
    </table>
</div>

<?php // Required vars: $cfg, $ciclo, $estudiantes ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:9pt; color:#1f2937; }
    table { width:100%; border-collapse:collapse; }
    .titulo-doc { text-align:center; font-size:13pt; font-weight:700; color:#1e3a6e;
                  letter-spacing:.08em; margin:14px 0 4px; text-transform:uppercase; }
    .subtitulo-doc { text-align:center; font-size:9pt; color:#6b7280; margin-bottom:14px; }
    .tabla thead tr { background:#1e3a6e; color:#fff; }
    .tabla th { padding:7px 8px; font-size:8pt; text-align:left; font-weight:700; }
    .tabla td { padding:6px 8px; border-bottom:1px solid #e5e7eb; font-size:8.5pt; vertical-align:middle; }
    .tabla tr:nth-child(even) td { background:#f8fafc; }
    .pie { font-size:7pt; color:#9ca3af; text-align:center; margin-top:18px; border-top:1px solid #e5e7eb; padding-top:8px; }
    .total-badge { display:inline-block; background:#1e3a6e; color:#fff; font-size:8pt;
                   padding:3px 10px; border-radius:20px; margin-bottom:10px; }
</style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="titulo-doc">Listado de Alumnado</div>
<div class="subtitulo-doc">
    <?= htmlspecialchars($ciclo['nombreCiclo'] ?? 'Todos los Ciclos') ?>
    — Curso <?= htmlspecialchars($cfg['cursoEscolar']) ?>
</div>
<div style="text-align:center; margin-bottom:12px;">
    <span class="total-badge"><?= count($estudiantes) ?> alumnos/as</span>
</div>

<table class="tabla" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="5%">Nº</th>
            <th width="30%">Nombre Completo</th>
            <th width="12%">DNI</th>
            <th width="28%">Email</th>
            <th width="14%">Teléfono</th>
            <th width="11%">Ciclo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($estudiantes as $i => $est): ?>
        <tr>
            <td style="text-align:center; color:#9ca3af;"><?= $i + 1 ?></td>
            <td style="font-weight:700;"><?= htmlspecialchars(strtoupper($est['nombreEstudiante'])) ?></td>
            <td><?= htmlspecialchars($est['dniEstudiante'] ?? '—') ?></td>
            <td style="font-size:7.5pt;"><?= htmlspecialchars($est['emailEstudiante'] ?? '—') ?></td>
            <td><?= htmlspecialchars($est['telefonoEstudiante'] ?? '—') ?></td>
            <td style="font-size:7.5pt;"><?= htmlspecialchars($est['abreviaturaCiclo'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table style="margin-top:40px;" cellpadding="0" cellspacing="0">
    <tr>
        <td width="40%" style="text-align:center;">
            <div style="border-top:1px solid #374151; padding-top:4px; margin-top:32px;">
                <?= htmlspecialchars($cfg['ciudadCentro'] ?? '') ?>, <?= date('d/m/Y') ?><br>
                <span style="font-size:8pt; color:#6b7280;">Fecha</span>
            </div>
        </td>
        <td width="20%"></td>
        <td width="40%" style="text-align:center;">
            <div style="border-top:1px solid #374151; padding-top:4px; margin-top:32px;">
                <?= htmlspecialchars($cfg['nombreDirectorFirmante'] ?? '') ?><br>
                <span style="font-size:8pt; color:#6b7280;">Director/a del Centro</span>
            </div>
        </td>
    </tr>
</table>

<?php if ($cfg['textoLegal']): ?>
    <div class="pie"><?= htmlspecialchars($cfg['textoLegal']) ?></div>
<?php endif; ?>

</body>
</html>

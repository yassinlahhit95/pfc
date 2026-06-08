<?php
// Required vars: $cfg, $ciclo (array with ciclo info), $estudiante (array with modulos[])
$notas = $estudiante['modulos'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:9pt; color:#1f2937; }
    table { width:100%; border-collapse:collapse; }
    .titulo-doc { text-align:center; font-size:13pt; font-weight:700; color:#1e3a6e;
                  letter-spacing:.08em; margin:14px 0 10px; text-transform:uppercase; }
    .subtitulo-doc { text-align:center; font-size:9pt; color:#6b7280; margin-bottom:14px; }
    .info-bloque { background:#f8fafc; border:1px solid #e2e8f0; border-radius:4px;
                   padding:10px 14px; margin-bottom:14px; }
    .info-bloque td { padding:3px 8px 3px 0; font-size:9pt; }
    .info-label { color:#6b7280; font-size:8pt; text-transform:uppercase; font-weight:700; }
    .tabla-notas thead tr { background:#1e3a6e; color:#fff; }
    .tabla-notas th { padding:7px 8px; font-size:8pt; text-align:center; font-weight:700; }
    .tabla-notas th:first-child { text-align:left; }
    .tabla-notas td { padding:6px 8px; border-bottom:1px solid #e5e7eb; font-size:8.5pt; vertical-align:middle; }
    .tabla-notas td:not(:first-child) { text-align:center; }
    .tabla-notas tr:nth-child(even) td { background:#f8fafc; }
    .nota-aprobada { color:#16a34a; font-weight:700; }
    .nota-suspensa { color:#dc2626; font-weight:700; }
    .nota-pendiente { color:#9ca3af; }
    .firma-bloque { margin-top:40px; }
    .firma-bloque td { vertical-align:bottom; font-size:8.5pt; padding:0 10px; }
    .linea-firma { border-top:1px solid #374151; padding-top:4px; margin-top:32px; text-align:center; }
    .pie { font-size:7pt; color:#9ca3af; text-align:center; margin-top:18px; border-top:1px solid #e5e7eb; padding-top:8px; }
    .page-break { page-break-after: always; }
</style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="titulo-doc">Boletín de Calificaciones</div>
<div class="subtitulo-doc">
    Ciclo Formativo de <?= htmlspecialchars($ciclo['nombreNivel'] ?? '') ?> —
    Curso <?= htmlspecialchars($cfg['cursoEscolar']) ?>
</div>

<table class="info-bloque" cellpadding="0" cellspacing="0">
    <tr>
        <td width="50%">
            <div class="info-label">Alumno/a</div>
            <div style="font-size:11pt; font-weight:700; color:#1e3a6e;">
                <?= htmlspecialchars(strtoupper($estudiante['nombreEstudiante'])) ?>
            </div>
        </td>
        <td width="20%">
            <div class="info-label">DNI</div>
            <div><?= htmlspecialchars($estudiante['dniEstudiante'] ?? '—') ?></div>
        </td>
        <td width="30%">
            <div class="info-label">Ciclo Formativo</div>
            <div><?= htmlspecialchars($estudiante['nombreCiclo'] ?? '') ?></div>
        </td>
    </tr>
</table>

<table class="tabla-notas" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th style="width:38%">Módulo Profesional</th>
            <th>1ª Eval.</th>
            <th>1ª Final</th>
            <th>2ª Eval.</th>
            <th>2ª Final</th>
            <th style="width:22%">Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($notas as $mod):
            $n = $mod['notas'];
            $nota2final = $n['nota_2final'] ?? null;
            $nota1final = $n['nota_1final'] ?? null;
            $definitiva = $nota2final ?? $nota1final;
            $clase = $definitiva === null ? 'nota-pendiente' : ($definitiva >= 5 ? 'nota-aprobada' : 'nota-suspensa');
            $fmt = fn($v) => $v !== null ? number_format((float)$v, 1) : '—';
        ?>
        <tr>
            <td><?= htmlspecialchars($mod['nombreModulo']) ?></td>
            <td><?= $fmt($n['nota_1ev']    ?? null) ?></td>
            <td class="<?= $clase ?>"><?= $fmt($nota1final) ?></td>
            <td><?= $fmt($n['nota_2ev']    ?? null) ?></td>
            <td class="<?= $clase ?>"><?= $fmt($nota2final) ?></td>
            <td style="font-size:7.5pt; color:#6b7280;"><?= htmlspecialchars($n['observaciones'] ?? '') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<table class="firma-bloque" cellpadding="0" cellspacing="0">
    <tr>
        <td width="40%">
            <div class="linea-firma">
                <?= htmlspecialchars($cfg['ciudadCentro'] ?? '') ?>, <?= date('d/m/Y') ?><br>
                <span style="color:#6b7280;">Fecha</span>
            </div>
        </td>
        <td width="20%"></td>
        <td width="40%">
            <div class="linea-firma">
                <?= htmlspecialchars($cfg['nombreDirectorFirmante'] ?? '') ?><br>
                <span style="color:#6b7280;">Director/a del Centro</span>
            </div>
        </td>
    </tr>
</table>

<?php if ($cfg['textoLegal']): ?>
    <div class="pie"><?= htmlspecialchars($cfg['textoLegal']) ?></div>
<?php endif; ?>

</body>
</html>

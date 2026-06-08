<?php // Required vars: $cfg, $ciclo, $franjas, $dias, $celdas ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:8pt; color:#1f2937; }
    table { width:100%; border-collapse:collapse; }
    .titulo-doc { text-align:center; font-size:13pt; font-weight:700; color:#1e3a6e;
                  letter-spacing:.08em; margin:14px 0 4px; text-transform:uppercase; }
    .subtitulo-doc { text-align:center; font-size:9pt; color:#6b7280; margin-bottom:14px; }
    .tabla-horario thead tr { background:#1e3a6e; color:#fff; }
    .tabla-horario th { padding:7px 6px; font-size:8pt; text-align:center; font-weight:700; border:1px solid #2d4e8a; }
    .tabla-horario td { padding:5px 4px; border:1px solid #e5e7eb; text-align:center; vertical-align:middle; font-size:7.5pt; height:42px; }
    .td-hora { background:#f8fafc; font-weight:700; color:#374151; font-size:7.5pt; white-space:nowrap; }
    .td-recreo { background:#fef3c7; color:#92400e; font-style:italic; font-size:7pt; }
    .celda-asig { border-radius:3px; padding:3px; color:#fff; font-size:7pt; line-height:1.3; }
    .celda-modulo { font-weight:700; }
    .celda-prof { font-size:6.5pt; opacity:.9; }
    .celda-aula { font-size:6pt; opacity:.8; }
    .pie { font-size:7pt; color:#9ca3af; text-align:center; margin-top:14px; border-top:1px solid #e5e7eb; padding-top:8px; }
</style>
</head>
<body>

<?php include __DIR__ . '/_header.php'; ?>

<div class="titulo-doc">Cuadro de Horario Semanal</div>
<div class="subtitulo-doc">
    <?= htmlspecialchars($ciclo['nombreCiclo']) ?> (<?= htmlspecialchars($ciclo['abreviaturaCiclo']) ?>)
    — Curso <?= htmlspecialchars($cfg['cursoEscolar']) ?>
</div>

<?php
$paleta = ['#667eea','#0ea5e9','#10b981','#e482ae','#5260b2','#f59e0b','#ef4444','#14b8a6'];
$colorMap = [];
$colorIdx = 0;
?>

<table class="tabla-horario" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th width="10%">Hora</th>
            <?php foreach ($dias as $dia): ?>
                <th><?= htmlspecialchars($dia) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($franjas as $f):
            $clave_base = substr($f['inicio'], 0, 5);
        ?>
        <tr>
            <td class="td-hora"><?= $f['inicio'] ?>–<?= $f['fin'] ?></td>
            <?php if ($f['recreo']): ?>
                <?php foreach ($dias as $d): ?>
                    <td class="td-recreo">— Recreo —</td>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($dias as $dia):
                    $clave = $dia . '|' . $clave_base;
                    $celda = $celdas[$clave] ?? null;
                ?>
                <td>
                    <?php if ($celda):
                        $idMod = $celda['idModulo'];
                        if (!isset($colorMap[$idMod])) {
                            $colorMap[$idMod] = $paleta[$colorIdx % count($paleta)];
                            $colorIdx++;
                        }
                        $color = $colorMap[$idMod];
                    ?>
                    <div class="celda-asig" style="background:<?= $color ?>;">
                        <div class="celda-modulo"><?= htmlspecialchars($celda['nombreModulo'] ?? '') ?></div>
                        <div class="celda-prof"><?= htmlspecialchars($celda['nombreProfesor'] ?? '') ?></div>
                        <?php if (!empty($celda['codigoAula'])): ?>
                            <div class="celda-aula"><?= htmlspecialchars($celda['codigoAula']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </td>
                <?php endforeach; ?>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($cfg['textoLegal']): ?>
    <div class="pie"><?= htmlspecialchars($cfg['textoLegal']) ?></div>
<?php endif; ?>

</body>
</html>

<?php // Required vars: $cfg, $ciclo, $franjas, $dias, $celdas ?>
<style>
    @page {
        header: page-header;
        footer: page-footer;
        margin-top: 35mm;
    }
    body { font-family: 'Roboto', sans-serif; color: #334155; }
    
    .titulo-doc { text-align:center; font-size:16pt; font-weight:700; color:#1e3a6e;
                  letter-spacing:.05em; margin: 0 0 5px; text-transform:uppercase; }
    .subtitulo-doc { text-align:center; font-size:10pt; color:#64748b; margin-bottom:15px; }

    .tabla-horario { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .tabla-horario th { background: #1e3a6e; color: #ffffff; padding: 10px 5px; font-size: 8.5pt; text-align: center; border: 1px solid #1e3a6e; text-transform: uppercase; }
    .tabla-horario td { border: 1px solid #e2e8f0; padding: 4px; height: 50px; vertical-align: middle; text-align: center; font-size: 8pt; }
    
    .td-hora { background: #f8fafc; font-weight: bold; color: #475569; width: 85px; }
    .td-recreo { background: #fffbeb; color: #b45309; font-weight: bold; font-size: 7.5pt; text-transform: uppercase; letter-spacing: 0.1em; }
    
    .celda-asig { padding: 4px; border-radius: 4px; color: #ffffff; height: 100%; display: block; }
    .celda-modulo { font-weight: bold; font-size: 7.5pt; line-height: 1.1; margin-bottom: 2px; }
    .celda-prof { font-size: 6.5pt; opacity: 0.95; }
    .celda-aula { font-size: 6.5pt; margin-top: 2px; font-weight: bold; background: rgba(0,0,0,0.1); border-radius: 2px; display: inline-block; padding: 0 4px; }
    
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
        Cuadro Horario Semanal — Generado por AulaPro — Página {PAGENO} de {nb}
    </div>
</htmlpagefooter>

<div class="titulo-doc">Horario Semanal</div>
<div class="subtitulo-doc">
    <?= htmlspecialchars($ciclo['nombreCiclo']) ?> (<?= htmlspecialchars($ciclo['abreviaturaCiclo']) ?>)
    — Curso <?= htmlspecialchars($cfg['cursoEscolar']) ?>
</div>

<?php
$paleta = ['#667eea','#0ea5e9','#10b981','#e482ae','#5260b2','#f59e0b','#ef4444','#14b8a6'];
$colorMap = [];
$colorIdx = 0;
?>

<table class="tabla-horario">
    <thead>
        <tr>
            <th style="width: 85px;">Hora</th>
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
            <td class="td-hora"><?= $f['inicio'] ?> – <?= $f['fin'] ?></td>
            <?php if ($f['recreo']): ?>
                <td colspan="5" class="td-recreo">
                    <img src="https://cdn-icons-png.flaticon.com/16/2913/2913465.png" style="vertical-align:middle; width:12px;"> DESCANSO
                </td>
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
                            <div class="celda-aula">Aula <?= htmlspecialchars($celda['codigoAula']) ?></div>
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

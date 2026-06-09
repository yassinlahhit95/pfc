<?php
// Required vars: $cfg, $ciclo, $estudiante, $notas

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;

// Serial is always pre-computed and signed by the controller
$_serial = $estudiante['_serial'] ?? '';

// Verification URL encoded in the QR
$_verifyUrl = rtrim($baseUrl ?? '', '/') . '/verificar/?s=' . urlencode($_serial);

// Build QR PNG as base64 data URI
try {
    $_qrResult = (new Builder(
        data: $_verifyUrl,
        size: 90,
        margin: 4,
        foregroundColor: new Color(30, 58, 110),
        backgroundColor: new Color(255, 255, 255),
        errorCorrectionLevel: ErrorCorrectionLevel::Medium,
    ))->build();
    $_qrSrc = 'data:image/png;base64,' . base64_encode($_qrResult->getString());
} catch (\Throwable $e) {
    $_qrSrc = null;
}
?>
<style>
    @page {
        header: page-header;
        footer: page-footer;
    }
    body { font-family: 'Roboto', sans-serif; color: #334155; }
    
    .card { background: #ffffff; padding: 0; }
    
    .student-info { margin-bottom: 25px; width: 100%; border-collapse: collapse; }
    .student-info td { padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; }
    .label { font-size: 8pt; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
    .value { font-size: 11pt; color: #1e293b; font-weight: bold; }

    .table-notas { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .table-notas th { background: #1e3a6e; color: #ffffff; padding: 12px 8px; font-size: 9pt; text-align: center; }
    .table-notas th:first-child { text-align: left; border-top-left-radius: 8px; }
    .table-notas th:last-child { border-top-right-radius: 8px; }
    
    .table-notas td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; text-align: center; }
    .table-notas td:first-child { text-align: left; font-weight: 500; }
    
    .row-even { background: #fdfdfd; }
    .row-odd { background: #ffffff; }

    .nota-circle {
        display: inline-block;
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 15px;
        text-align: center;
        font-weight: bold;
        color: #fff;
    }
    .nota-aprobada { background-color: #10b981; }
    .nota-suspensa { background-color: #ef4444; }
    .nota-vacia { color: #cbd5e1; }
    .nota-especial { background-color: #64748b; font-size: 7pt; width: 34px; }

    .signature-area { margin-top: 50px; width: 100%; }
    .signature-box { border-top: 1px solid #94a3b8; padding-top: 10px; text-align: center; font-size: 9pt; }
    
    .header-table { width: 100%; border-bottom: 2px solid #1e3a6e; padding-bottom: 15px; margin-bottom: 20px; }
</style>

<htmlpageheader name="page-header">
    <table class="header-table">
        <tr>
            <td width="20%"><img src="<?= logoParaPdf($cfg['logoGobierno1']) ?>" style="max-height: 50px;"></td>
            <td width="60%" align="center">
                <div style="font-size: 16pt; font-weight: bold; color: #1e3a6e;"><?= htmlspecialchars($cfg['nombreCentro']) ?></div>
                <div style="font-size: 9pt; color: #64748b;"><?= htmlspecialchars($cfg['direccionCentro']) ?></div>
            </td>
            <td width="20%" align="right"><img src="<?= logoParaPdf($cfg['logoCentro'] ?: $cfg['logoGobierno2']) ?>" style="max-height: 50px;"></td>
        </tr>
    </table>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div style="border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 8pt; color: #94a3b8; text-align: center;">
        <?= htmlspecialchars($cfg['nombreCentro']) ?> — Documento oficial generado por AulaPro — Página {PAGENO} de {nb}
    </div>
</htmlpagefooter>

<div class="card">
    <div style="text-align: center; margin-bottom: 20px;">
        <h1 style="font-size: 18pt; color: #1e293b; margin: 0;">BOLETÍN DE CALIFICACIONES</h1>
        <div style="color: #64748b; font-size: 10pt; margin-top: 5px;">Curso Académico <?= htmlspecialchars($cfg['cursoEscolar']) ?></div>
    </div>

    <table class="student-info">
        <tr>
            <td width="60%">
                <div class="label">Alumno/a</div>
                <div class="value"><?= mb_strtoupper(htmlspecialchars($estudiante['nombreEstudiante'])) ?></div>
            </td>
            <td width="40%">
                <div class="label">Especialidad</div>
                <div class="value"><?= htmlspecialchars($ciclo['abreviaturaCiclo']) ?></div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Nivel</div>
                <div class="value"><?= htmlspecialchars($ciclo['nombreNivel'] ?? '—') ?></div>
            </td>
            <td>
                <div class="label">Expediente / DNI</div>
                <div class="value"><?= htmlspecialchars($estudiante['dniEstudiante'] ?: '—') ?></div>
            </td>
        </tr>
    </table>

    <table class="table-notas">
        <thead>
            <tr>
                <th width="40%">Módulo Profesional</th>
                <th width="12%">1ª Eval.</th>
                <th width="12%">1ª Final</th>
                <th width="12%">2ª Eval.</th>
                <th width="12%">2ª Final</th>
                <th width="12%">Def.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $defsParaMedia = [];
            $especiales = ['NP', 'EX', 'CO'];
            $etiquetaEst = ['NP'=>'No Presentado','EX'=>'Exento','CO'=>'Convalidado'];
            // Returns display string for one eval cell
            $fmtCell = function($nota, $estado) use ($especiales) {
                if ($estado && in_array($estado, $especiales, true)) return $estado;
                return ($nota !== null) ? number_format((float)$nota, 1) : '—';
            };
            // Returns definitive value (string NP/EX/CO or float) or null
            $defVal = function($n) use ($especiales) {
                $pairs = [
                    [$n['nota_2final'] ?? null, $n['estado_2final'] ?? null],
                    [$n['nota_2ev']    ?? null, $n['estado_2ev']    ?? null],
                    [$n['nota_1final'] ?? null, $n['estado_1final'] ?? null],
                    [$n['nota_1ev']    ?? null, $n['estado_1ev']    ?? null],
                ];
                foreach ($pairs as [$nota, $est]) {
                    if ($est && in_array($est, $especiales, true)) return $est;
                    if ($nota !== null) return (float)$nota;
                }
                return null;
            };
            foreach ($notas as $mod):
                $n   = $mod['notas'] ?? [];
                $def = $defVal($n);
                if (is_float($def)) $defsParaMedia[] = $def;
                $claseDef = is_string($def) ? 'nota-especial'
                          : (is_float($def) ? ($def >= 5 ? 'nota-aprobada' : 'nota-suspensa') : 'nota-vacia');
                $i++;
            ?>
            <tr class="<?= $i % 2 == 0 ? 'row-even' : 'row-odd' ?>">
                <td><?= htmlspecialchars($mod['nombreModulo']) ?></td>
                <?php if ($def === 'CO'): ?>
                <td colspan="4" style="text-align:center; background:#f0fdf4; color:#166534; font-weight:bold; font-size:9pt;">
                    &#10003; Convalidado
                </td>
                <?php else: ?>
                <td><?= $fmtCell($n['nota_1ev'] ?? null,    $n['estado_1ev']    ?? null) ?></td>
                <td><b><?= $fmtCell($n['nota_1final'] ?? null, $n['estado_1final'] ?? null) ?></b></td>
                <td><?= $fmtCell($n['nota_2ev'] ?? null,    $n['estado_2ev']    ?? null) ?></td>
                <td><b><?= $fmtCell($n['nota_2final'] ?? null, $n['estado_2final'] ?? null) ?></b></td>
                <?php endif; ?>
                <td>
                    <div class="nota-circle <?= $claseDef ?>">
                        <?= is_string($def) ? $def : (is_float($def) ? number_format($def, 0) : '—') ?>
                    </div>
                </td>
            </tr>
            <?php endforeach;

            $notaTFG = isset($estudiante['nota_tfg']) && $estudiante['nota_tfg'] !== null
                ? (float)$estudiante['nota_tfg'] : null;
            if ($notaTFG !== null) $defsParaMedia[] = $notaTFG;
            $claseTFG = ($notaTFG !== null) ? ($notaTFG >= 5 ? 'nota-aprobada' : 'nota-suspensa') : 'nota-vacia';
            ?>
            <tr style="background:#eef2ff; border-top: 2px solid #1e3a6e;">
                <td style="font-weight:bold; color:#1e3a6e;">Trabajo de Fin de Grado (TFG)</td>
                <td>—</td>
                <td>—</td>
                <td>—</td>
                <td>—</td>
                <td>
                    <div class="nota-circle <?= $claseTFG ?>">
                        <?= ($notaTFG !== null) ? number_format($notaTFG, 0) : '—' ?>
                    </div>
                </td>
            </tr>
            <?php
            $mediaTotal = count($defsParaMedia) > 0
                ? array_sum($defsParaMedia) / count($defsParaMedia) : null;
            $claseMedia = ($mediaTotal !== null) ? ($mediaTotal >= 5 ? 'nota-aprobada' : 'nota-suspensa') : 'nota-vacia';
            ?>
            <tr style="background:#1e3a6e;">
                <td style="color:#ffffff; font-weight:bold; font-size:10pt;">NOTA MEDIA FINAL</td>
                <td colspan="4" style="color:#94a3b8; font-style:italic; font-size:8pt; text-align:center;">Media de todas las calificaciones definitivas</td>
                <td>
                    <div class="nota-circle <?= $claseMedia ?>">
                        <?= ($mediaTotal !== null) ? number_format($mediaTotal, 1) : '—' ?>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="signature-area">
        <tr>
            <td width="35%">
                <div class="signature-box" style="border:none; text-align: left;">
                    <?php
                    $meses = ["", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                    $mes = $meses[(int)date('m')];
                    ?>
                    <?= htmlspecialchars($cfg['ciudadCentro']) ?>, a <?= date('d') ?> de <?= $mes ?> de <?= date('Y') ?>
                </div>
            </td>
            <td width="30%">
                <div class="signature-box">
                    Fdo: <strong><?= htmlspecialchars($cfg['nombreDirectorFirmante']) ?></strong><br>
                    Director/a del Centro
                </div>
            </td>
            <td width="35%" style="text-align:center; vertical-align:bottom;">
                <?php if ($_qrSrc): ?>
                <img src="<?= $_qrSrc ?>" style="width:75px; height:75px; display:block; margin:0 auto 4px;">
                <?php endif; ?>
                <div style="font-size:6.5pt; color:#64748b; letter-spacing:.5px; font-family:monospace;">
                    <?= htmlspecialchars($_serial) ?>
                </div>
                <div style="font-size:6pt; color:#94a3b8; margin-top:1px;">
                    Documento oficial verificable
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; padding: 8px 14px; background: #f8fafc; border: 1px solid #e2e8f0;">
        <table style="width:100%; font-size: 7.5pt; color: #334155; border-collapse: collapse;">
            <tr>
                <td style="color:#1e3a6e; font-weight:bold; white-space:nowrap; padding-right:10px;">GLOSARIO:</td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>SB</strong> Sobresaliente <span style="color:#64748b;">9–10</span></td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>NT</strong> Notable <span style="color:#64748b;">7–8</span></td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>BI</strong> Bien <span style="color:#64748b;">6</span></td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>SF</strong> Suficiente <span style="color:#64748b;">5</span></td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>IN</strong> Insuficiente <span style="color:#64748b;">1–4</span></td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>NP</strong> No Presentado</td>
                <td style="white-space:nowrap; padding-right:6px;"><strong>EX</strong> Exento</td>
                <td style="white-space:nowrap;"><strong>CO</strong> Convalidado</td>
            </tr>
        </table>
    </div>
</div>

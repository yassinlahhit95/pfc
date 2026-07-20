<?php
// Required vars: $cfg, $ciclo, $estudiante, $notas

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;

// Serial is always pre-computed and signed by the controller
$_serial = $estudiante['_serial'] ?? '';

// Grado Superior es el único nivel con Módulo de Proyecto — Grado Medio no
// lo tiene. La comparación es por texto porque nombreNivel es libre (lo
// escribe el centro en Niveles), no un enum fijo.
$_esGradoSuperior = stripos($ciclo['nombreNivel'] ?? '', 'superior') !== false;

// Verification URL encoded in the QR
// Prefer real HTTP_HOST to avoid localhost when APP_URL is not updated for production
if (!empty($_SERVER['HTTP_HOST'])) {
    $_scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $_host     = $_SERVER['HTTP_HOST'];
    // Base path derived from the live request, never from APP_URL: the env value
    // may point to another install (e.g. local /pfc) and would break the QR link.
    $_script   = $_SERVER['SCRIPT_NAME'] ?? '';
    $_pos      = strpos($_script, '/controladores/');
    $_appPath  = ($_pos !== false) ? substr($_script, 0, $_pos) : '';
    $_realBase = $_scheme . '://' . $_host . $_appPath;
} else {
    $_realBase = rtrim($baseUrl ?? '', '/');
}
$_verifyUrl = $_realBase . '/verificar/index.php?s=' . urlencode($_serial);

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
    error_log('[' . ($cfg['nombreCentro'] ?? 'AulaPro') . '] Boletin QR generation failed: ' . $e->getMessage());
    $_qrSrc = null;
}

// Estado global -> color/etiqueta del badge de resumen
$_estadoGlobal = strtoupper($estudiante['estado_global'] ?? 'PENDIENTE');
$_estadoColores = [
    'APROBADO' => ['bg' => '#dcfce7', 'fg' => '#166534'],
    'SUSPENSO' => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
    'PENDIENTE' => ['bg' => '#f1f5f9', 'fg' => '#475569'],
];
$_estadoColor = $_estadoColores[$_estadoGlobal] ?? $_estadoColores['PENDIENTE'];
?>
<style>
    @page {
        header: html_page-header;
        footer: html_page-footer;
    }
    body { font-family: 'Roboto', sans-serif; color: #334155; }

    .card { background: #ffffff; padding: 0; }

    .summary-bar { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .summary-bar td { padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; vertical-align: middle; }
    .label { font-size: 8pt; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; letter-spacing: .04em; }
    .value { font-size: 11pt; color: #1e293b; font-weight: bold; }
    .value-sm { font-size: 9.5pt; color: #1e293b; font-weight: 600; }

    .estado-badge {
        display: inline-block; padding: 6px 16px; border-radius: 20px;
        font-size: 10pt; font-weight: bold; letter-spacing: .03em;
    }

    .curso-header td {
        background: #1e3a6e; color: #ffffff; font-size: 9pt; font-weight: bold;
        padding: 6px 10px; text-transform: uppercase; letter-spacing: .04em;
    }

    .table-notas { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .table-notas th { background: #eef2ff; color: #1e3a6e; padding: 10px 8px; font-size: 8.5pt; text-align: center; border-bottom: 2px solid #1e3a6e; }
    .table-notas th:first-child { text-align: left; }

    .table-notas td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; text-align: center; }
    .table-notas td:first-child { text-align: left; font-weight: 500; }

    .row-even { background: #fdfdfd; }
    .row-odd { background: #ffffff; }

    .fila-especial td { background: #f8fafc; }

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

    .signature-area { margin-top: 40px; width: 100%; }
    .signature-box { border-top: 1px solid #94a3b8; padding-top: 10px; text-align: center; font-size: 9pt; }
</style>

<htmlpageheader name="page-header">
    <?php include __DIR__ . '/_header.php'; ?>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div style="border-top: 1px solid #e2e8f0; padding-top: 10px; font-size: 8pt; color: #94a3b8; text-align: center;">
        <?= htmlspecialchars($cfg['nombreCentro']) ?> — Documento oficial — Página {PAGENO} de {nb}
    </div>
</htmlpagefooter>

<div class="card">
    <div style="text-align: center; margin-bottom: 18px;">
        <h1 style="font-size: 18pt; color: #1e293b; margin: 0;">BOLETÍN DE CALIFICACIONES</h1>
        <div style="color: #64748b; font-size: 10pt; margin-top: 5px;">Curso Académico <?= htmlspecialchars($cfg['cursoEscolar']) ?></div>
    </div>

    <table class="summary-bar">
        <tr>
            <td width="45%">
                <div class="label">Alumno/a</div>
                <div class="value"><?= mb_strtoupper(htmlspecialchars($estudiante['nombreEstudiante'])) ?></div>
            </td>
            <td width="30%">
                <div class="label">Ciclo Formativo</div>
                <div class="value-sm"><?= htmlspecialchars($ciclo['nombreCiclo']) ?> (<?= htmlspecialchars($ciclo['abreviaturaCiclo']) ?>)</div>
            </td>
            <td width="25%" style="text-align:center;">
                <div class="label">Estado</div>
                <span class="estado-badge" style="background:<?= $_estadoColor['bg'] ?>; color:<?= $_estadoColor['fg'] ?>;"><?= htmlspecialchars($_estadoGlobal) ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <div class="label">Nivel</div>
                <div class="value-sm"><?= htmlspecialchars($ciclo['nombreNivel'] ?? '—') ?></div>
            </td>
            <td>
                <div class="label">Expediente / DNI</div>
                <div class="value-sm"><?= htmlspecialchars($estudiante['dniEstudiante'] ?: '—') ?></div>
            </td>
            <td style="text-align:center;">
                <div class="label">Nota media</div>
                <div class="value-sm"><?= is_numeric($estudiante['promedio_global'] ?? null) ? number_format((float)$estudiante['promedio_global'], 2) : '—' ?></div>
            </td>
        </tr>
    </table>

    <?php
    $especiales = ['NP', 'EX', 'CO'];
    $etiquetaEst = ['NP'=>'No Presentado','EX'=>'Exento','CO'=>'Convalidado'];
    // Returns display string for one eval cell
    $fmtCell = function($nota, $estado) use ($especiales) {
        if ($estado && in_array($estado, $especiales, true)) return $estado;
        return ($nota !== null) ? number_format((float)$nota, 1) : '—';
    };
    // Solo detecta un código especial (NP/EX/CO) para saber si mostrar la
    // fila de "Convalidado"/etc — la NOTA numérica ya no se recalcula aquí,
    // viene resuelta por listarResultadosFinalesCiclo() (mismo motor que
    // el resto de la app: motor configurable / RA-CE / calcularNotaDefinitiva
    // + peso examen-reto), para que el boletín nunca pueda mostrar un número
    // distinto al que ve el alumno en su panel.
    $codigoEspecial = function($n) use ($especiales) {
        foreach ([$n['estado_2final'] ?? null, $n['estado_2ev'] ?? null, $n['estado_1final'] ?? null, $n['estado_1ev'] ?? null] as $est) {
            if ($est && in_array($est, $especiales, true)) return $est;
        }
        return null;
    };

    // Agrupados por curso (1º, 2º...) — un ciclo de FP dura varios años y
    // mezclar sus módulos en una sola lista sin distinguir el curso confundía
    // el expediente.
    $modulosPorAnio = [];
    foreach ($notas as $mod) {
        $modulosPorAnio[(int)($mod['cursoAnio'] ?? 1)][] = $mod;
    }
    ksort($modulosPorAnio);
    ?>

    <table class="table-notas">
        <thead>
            <tr>
                <th width="8%">Código</th>
                <th width="32%">Módulo Profesional</th>
                <th width="12%">1ª Eval.</th>
                <th width="12%">1ª Final</th>
                <th width="12%">2ª Eval.</th>
                <th width="12%">2ª Final</th>
                <th width="12%">Def.</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; foreach ($modulosPorAnio as $_anio => $_modulosAnio): ?>
            <tr class="curso-header"><td colspan="7"><?= $_anio ?>º Curso</td></tr>
            <?php foreach ($_modulosAnio as $mod):
                $n   = $mod['notas'] ?? [];
                $codigo = $codigoEspecial($n);
                $notaFinalModulo = $mod['nota_final'] ?? '-';
                if ($codigo !== null) {
                    $claseDef = 'nota-especial';
                } elseif (is_numeric($notaFinalModulo)) {
                    $claseDef = ($mod['estado'] ?? '') === 'Aprobado' ? 'nota-aprobada' : 'nota-suspensa';
                } else {
                    $claseDef = 'nota-vacia';
                }
                $i++;
            ?>
            <tr class="<?= $i % 2 == 0 ? 'row-even' : 'row-odd' ?>">
                <td style="text-align:center; font-size:8pt; color:#64748b;"><?= htmlspecialchars($mod['codigoModulo'] ?? '') ?: '—' ?></td>
                <td><?= htmlspecialchars($mod['nombreModulo']) ?></td>
                <?php if ($codigo === 'CO'): ?>
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
                        <?= $codigo !== null ? $codigo : (is_numeric($notaFinalModulo) ? number_format((float)$notaFinalModulo, 0) : '—') ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; endforeach; ?>

            <?php
            // FCT: obligatoria en ambos grados para poder titular. Se muestra
            // siempre (aunque todavía no tenga nota) para que su ausencia sea
            // visible en el expediente, no invisible como pasaba antes.
            $notaFCT = $estudiante['nota_fct'] ?? null;
            $claseFCT = ($notaFCT !== null) ? ($notaFCT >= 5 ? 'nota-aprobada' : 'nota-suspensa') : 'nota-vacia';
            ?>
            <tr class="fila-especial" style="border-top: 2px solid #1e3a6e;">
                <td>—</td>
                <td style="font-weight:bold; color:#1e3a6e;">Formación en Centros de Trabajo (FCT)</td>
                <td>—</td><td>—</td><td>—</td><td>—</td>
                <td>
                    <div class="nota-circle <?= $claseFCT ?>">
                        <?= ($notaFCT !== null) ? number_format($notaFCT, 0) : '—' ?>
                    </div>
                </td>
            </tr>

            <?php
            // notaAprobado: 5 salvo que el motor configurable esté activo con otro
            // valor — se usa tanto para el Proyecto como para la media global.
            $notaAprobado = 5.0;
            $notaMinimaTfgPdf = 5.0;
            if (motorAcademicoActivo()) {
                $configActiva = obtenerConfigAcademicaActiva();
                if ($configActiva) {
                    $politica = obtenerPoliticaCalificacion((int)$configActiva['idConfig']);
                    if ($politica) $notaAprobado = (float)$politica['notaAprobado'];
                    // El Proyecto usa su propio mínimo (tfg_config.notaMinima) si existe.
                    $configTFGPdf = obtenerConfigTFG((int)$configActiva['idConfig']);
                    if ($configTFGPdf) $notaMinimaTfgPdf = (float)$configTFGPdf['notaMinima'];
                }
            }

            // Módulo de Proyecto: SOLO existe en Grado Superior — Grado Medio
            // no lo tiene, así que antes no debía aparecer en su boletín
            // (aunque el centro tuviera la entrega de TFG activada).
            if ($_esGradoSuperior):
                $notaTFG = isset($estudiante['nota_tfg']) && $estudiante['nota_tfg'] !== null
                    ? (float)$estudiante['nota_tfg'] : null;
                $claseTFG = ($notaTFG !== null) ? ($notaTFG >= $notaMinimaTfgPdf ? 'nota-aprobada' : 'nota-suspensa') : 'nota-vacia';
            ?>
            <tr class="fila-especial">
                <td>—</td>
                <td style="font-weight:bold; color:#1e3a6e;">Módulo de Proyecto</td>
                <td>—</td><td>—</td><td>—</td><td>—</td>
                <td>
                    <div class="nota-circle <?= $claseTFG ?>">
                        <?= ($notaTFG !== null) ? number_format($notaTFG, 0) : '—' ?>
                    </div>
                </td>
            </tr>
            <?php endif;

            // Media global: ya resuelta por listarResultadosFinalesCiclo() (peso
            // examen/reto configurable + peso de TFG), no una media simple
            // recalculada aquí — antes esta plantilla ignoraba los retos por
            // completo al hacer la media, algo que ningún otro sitio de la app hacía.
            $mediaTotal = $estudiante['promedio_global'] ?? '-';
            $claseMedia = is_numeric($mediaTotal) ? ($mediaTotal >= $notaAprobado ? 'nota-aprobada' : 'nota-suspensa') : 'nota-vacia';
            ?>
            <tr style="background:#1e3a6e;">
                <td colspan="2" style="color:#ffffff; font-weight:bold; font-size:10pt;">NOTA MEDIA FINAL</td>
                <td colspan="4" style="color:#94a3b8; font-style:italic; font-size:8pt; text-align:center;">Media de todas las calificaciones definitivas</td>
                <td>
                    <div class="nota-circle <?= $claseMedia ?>">
                        <?= is_numeric($mediaTotal) ? number_format((float)$mediaTotal, 1) : '—' ?>
                    </div>
                </td>
            </tr>
            <tr style="background:#f8fafc;">
                <td colspan="7" style="padding:8px 10px;">
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

    <?php if (!empty($cfg['textoLegal'])): ?>
    <div style="margin-top:18px; padding-top:8px; border-top:1px solid #e2e8f0; font-size:7pt; color:#94a3b8; text-align:center;">
        <?= nl2br(htmlspecialchars($cfg['textoLegal'])) ?>
    </div>
    <?php endif; ?>

</div>

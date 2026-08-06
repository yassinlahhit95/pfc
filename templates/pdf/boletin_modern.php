<?php
// PDF: Boletín de Calificaciones (Grade Report)
// Required vars: $cfg, $ciclo, $estudiante, $notas
// Optional: $baseUrl (for QR verification URL)

include __DIR__ . '/_styles.php';
include __DIR__ . '/_helpers.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;

// Serial is always pre-computed and signed by the controller
$_serial = $estudiante['_serial'] ?? '';

// Grado Superior is the only level with Proyecto (Project) module
$_esGradoSuperior = stripos($ciclo['nombreNivel'] ?? '', 'superior') !== false;

// Build QR code for document verification
$_qrSrc = null;
if (!empty($_serial)) {
    if (!empty($_SERVER['HTTP_HOST'])) {
        $_scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $_host     = $_SERVER['HTTP_HOST'];
        $_script   = $_SERVER['SCRIPT_NAME'] ?? '';
        $_pos      = strpos($_script, '/controladores/');
        $_appPath  = ($_pos !== false) ? substr($_script, 0, $_pos) : '';
        $_realBase = $_scheme . '://' . $_host . $_appPath;
    } else {
        $_realBase = rtrim($baseUrl ?? '', '/');
    }
    $_verifyUrl = $_realBase . '/verificar/index.php?s=' . urlencode($_serial);

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
        error_log('[PDF Boletín] QR generation failed: ' . $e->getMessage());
    }
}

// Overall status badge
$_estadoGlobal = strtoupper($estudiante['estado_global'] ?? 'PENDIENTE');
$_estadoMap = [
    'APROBADO' => 'success',
    'SUSPENSO' => 'error',
    'PENDIENTE' => 'pending',
];
$_estadoBadgeClass = $_estadoMap[$_estadoGlobal] ?? 'pending';

$_notaAprobado = 5.0;
$_notaMinimaTfg = 5.0;
if (motorAcademicoActivo()) {
    $_configActiva = obtenerConfigAcademicaActiva();
    if ($_configActiva) {
        $_politica = obtenerPoliticaCalificacion((int)$_configActiva['idConfig']);
        if ($_politica) $_notaAprobado = (float)$_politica['notaAprobado'];
        $_configTFG = obtenerConfigTFG((int)$_configActiva['idConfig']);
        if ($_configTFG) $_notaMinimaTfg = (float)$_configTFG['notaMinima'];
    }
}
?>

<htmlpageheader name="page-header">
    <?php include __DIR__ . '/_header.php'; ?>
</htmlpageheader>

<?php include __DIR__ . '/_footer.php'; $cicloInfo = pdfAssertField($ciclo['nombreCiclo'] ?? null, 'ciclo.nombreCiclo') . ' (' . pdfAssertField($ciclo['abreviaturaCiclo'] ?? null, 'ciclo.abreviaturaCiclo') . ')'; ?>

<div style="text-align: center; margin-bottom: 18px;">
    <h1 class="pdf-title">BOLETÍN DE CALIFICACIONES</h1>
    <div class="pdf-subtitle">Curso Académico <?= pdfAssertField($cfg['cursoEscolar'] ?? null, 'cfg.cursoEscolar') ?></div>
</div>

<table class="pdf-summary-bar">
    <tr>
        <td width="45%">
            <div class="pdf-label">Alumno/a</div>
            <div class="pdf-value"><?= mb_strtoupper(pdfAssertField($estudiante['nombreEstudiante'] ?? null, 'estudiante.nombreEstudiante')) ?></div>
        </td>
        <td width="30%">
            <div class="pdf-label">Ciclo Formativo</div>
            <div class="pdf-value-small"><?= pdfAssertField($ciclo['nombreCiclo'] ?? null, 'ciclo.nombreCiclo') ?> (<?= pdfAssertField($ciclo['abreviaturaCiclo'] ?? null, 'ciclo.abreviaturaCiclo') ?>)</div>
        </td>
        <td width="25%" style="text-align:center;">
            <div class="pdf-label">Estado</div>
            <span class="pdf-badge <?= $_estadoBadgeClass ?>"><?= htmlspecialchars($_estadoGlobal, ENT_QUOTES, 'UTF-8') ?></span>
        </td>
    </tr>
    <tr>
        <td>
            <div class="pdf-label">Nivel</div>
            <div class="pdf-value-small"><?= pdfAssertField($ciclo['nombreNivel'] ?? null, 'ciclo.nombreNivel') ?></div>
        </td>
        <td>
            <div class="pdf-label">Expediente / DNI</div>
            <div class="pdf-value-small"><?= pdfAssertField($estudiante['dniEstudiante'] ?? null, 'estudiante.dniEstudiante') ?></div>
        </td>
        <td style="text-align:center;">
            <div class="pdf-label">Nota media</div>
            <div class="pdf-value-small"><?= pdfFormatNumber($estudiante['promedio_global'] ?? null, 2) ?></div>
        </td>
    </tr>
</table>

<?php
// Group modules by school year
$_modulosPorAnio = [];
foreach ($notas as $mod) {
    $_modulosPorAnio[(int)($mod['cursoAnio'] ?? 1)][] = $mod;
}
ksort($_modulosPorAnio);

// Determine special status code (NP, EX, CO)
$_codigoEspecial = function($n) {
    $especiales = ['NP', 'EX', 'CO'];
    foreach ([$n['estado_2final'] ?? null, $n['estado_2ev'] ?? null, $n['estado_1final'] ?? null, $n['estado_1ev'] ?? null] as $est) {
        if ($est && in_array($est, $especiales, true)) return $est;
    }
    return null;
};
?>

<table class="pdf-table">
    <thead>
        <tr class="pdf-table-header">
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
        <?php $_i = 0; foreach ($_modulosPorAnio as $_anio => $_modulosAnio): ?>
        <tr class="pdf-section-header"><td colspan="7"><?= $_anio ?>º Curso</td></tr>
        <?php foreach ($_modulosAnio as $_mod):
            $_n = $_mod['notas'] ?? [];
            $_codigo = $_codigoEspecial($_n);
            $_notaFinal = $_mod['nota_final'] ?? null;
            $_clase = pdfGradeClass($_notaFinal, $_codigo, (int)$_notaAprobado);
            $_i++;
        ?>
        <tr class="<?= $_i % 2 == 0 ? 'pdf-row-even' : 'pdf-row-odd' ?>">
            <td style="text-align:center;"><?= pdfAssertField($_mod['codigoModulo'] ?? null, 'modulo.codigoModulo') ?></td>
            <td><?= pdfAssertField($_mod['nombreModulo'] ?? null, 'modulo.nombreModulo') ?></td>
            <?php if ($_codigo === 'CO'): ?>
            <td colspan="4" style="text-align:center; background:#f0fdf4; color:#166534; font-weight:bold;">✓ Convalidado</td>
            <?php else: ?>
            <td><?= pdfFormatGradeCell($_n['nota_1ev'] ?? null, $_n['estado_1ev'] ?? null) ?></td>
            <td><strong><?= pdfFormatGradeCell($_n['nota_1final'] ?? null, $_n['estado_1final'] ?? null) ?></strong></td>
            <td><?= pdfFormatGradeCell($_n['nota_2ev'] ?? null, $_n['estado_2ev'] ?? null) ?></td>
            <td><strong><?= pdfFormatGradeCell($_n['nota_2final'] ?? null, $_n['estado_2final'] ?? null) ?></strong></td>
            <?php endif; ?>
            <td><div class="<?= $_clase ?>"><?= pdfGradeDisplay($_notaFinal, $_codigo) ?></div></td>
        </tr>
        <?php endforeach; endforeach; ?>

        <!-- FCT (Practicum) — mandatory for both levels -->
        <?php $_notaFCT = $estudiante['nota_fct'] ?? null; ?>
        <tr style="border-top: 2px solid #1e3a6e; background: #f8fafc;">
            <td>—</td>
            <td style="font-weight:bold; color:#1e3a6e;">Formación en Centros de Trabajo (FCT)</td>
            <td>—</td><td>—</td><td>—</td><td>—</td>
            <td><div class="<?= pdfGradeClass($_notaFCT, null, (int)$_notaAprobado) ?>"><?= pdfFormatNumber($_notaFCT) ?></div></td>
        </tr>

        <!-- Proyecto (Project) — Grado Superior only -->
        <?php if ($_esGradoSuperior):
            $_notaTFG = $estudiante['nota_tfg'] ?? null;
        ?>
        <tr style="background: #f8fafc;">
            <td>—</td>
            <td style="font-weight:bold; color:#1e3a6e;">Módulo de Proyecto</td>
            <td>—</td><td>—</td><td>—</td><td>—</td>
            <td><div class="<?= pdfGradeClass($_notaTFG, null, (int)$_notaMinimaTfg) ?>"><?= pdfFormatNumber($_notaTFG) ?></div></td>
        </tr>
        <?php endif; ?>

        <!-- Overall average -->
        <?php $_mediaTotal = $estudiante['promedio_global'] ?? null; ?>
        <tr style="background: #1e3a6e; color: white;">
            <td colspan="2" style="color:#fff; font-weight:bold;">NOTA MEDIA FINAL</td>
            <td colspan="4" style="color:#93c5fd; font-style:italic; text-align:center; font-size: 6.5pt;">Media de todas las calificaciones definitivas</td>
            <td><div class="pdf-grade-circle <?= is_numeric($_mediaTotal) && $_mediaTotal >= $_notaAprobado ? 'aprobado' : 'suspenso' ?>"><?= pdfFormatNumber($_mediaTotal, 1) ?></div></td>
        </tr>

        <!-- Legend -->
        <tr style="background: #f8fafc;">
            <td colspan="7" style="padding: 8px;">
                <table style="width:100%; font-size: 6.5pt; color: #1e293b; border-collapse: collapse;">
                    <tr>
                        <td style="color:#1e3a6e; font-weight:bold; white-space:nowrap; padding-right:16px;">GLOSARIO:</td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>SB</strong> Sobresaliente <span style="color:#94a3b8;">9–10</span></td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>NT</strong> Notable <span style="color:#94a3b8;">7–8</span></td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>BI</strong> Bien <span style="color:#94a3b8;">6</span></td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>SF</strong> Suficiente <span style="color:#94a3b8;">5</span></td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>IN</strong> Insuficiente <span style="color:#94a3b8;">1–4</span></td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>NP</strong> No Presentado</td>
                        <td style="white-space:nowrap; padding-right:6px;"><strong>EX</strong> Exento</td>
                        <td style="white-space:nowrap;"><strong>CO</strong> Convalidado</td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>

<table class="pdf-signature-area">
    <tr>
        <td width="35%">
            <div class="pdf-signature-box" style="border:none; text-align: left;">
                <?php
                $_meses = ["", "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];
                $_mes = $_meses[(int)date('m')];
                ?>
                <?= pdfAssertField($cfg['ciudadCentro'] ?? null, 'cfg.ciudadCentro') ?>, a <?= date('d') ?> de <?= $_mes ?> de <?= date('Y') ?>
            </div>
        </td>
        <td width="30%">
            <div class="pdf-signature-box">
                Fdo: <strong><?= pdfAssertField($cfg['nombreDirectorFirmante'] ?? null, 'cfg.nombreDirectorFirmante') ?></strong><br>
                Director/a del Centro
            </div>
        </td>
        <td width="35%" style="text-align:center; vertical-align:bottom;">
            <?php if ($_qrSrc): ?>
            <img src="<?= $_qrSrc ?>" alt="Código QR de verificación del documento" style="width:75px; height:75px; display:block; margin:0 auto 4px;">
            <?php endif; ?>
            <div style="font-size:6.5pt; color:#94a3b8; letter-spacing:.5px; font-family:monospace;">
                <?= htmlspecialchars($_serial, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div style="font-size:6.5pt; color:#94a3b8; margin-top:2px;">
                Documento oficial verificable
            </div>
        </td>
    </tr>
</table>

<?php if (!empty($cfg['textoLegal'])): ?>
<div class="pdf-legal">
    <?= nl2br(pdfAssertField($cfg['textoLegal'] ?? null, 'cfg.textoLegal')) ?>
</div>
<?php endif; ?>

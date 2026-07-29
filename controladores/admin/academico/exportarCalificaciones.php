<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";

$vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($vendor)) {
    $_SESSION['errores'] = "Ocurrió un error interno en el servidor al intentar exportar las notas. Por favor, póngase en contacto con el soporte técnico.";
    header("Location: ../../../vistas/admin/academico/resultadosFinales.php");
    exit;
}
require_once $vendor;
require_once __DIR__ . '/../../../modelos/calificaciones.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/academico/resultadosFinales.php");
    exit;
}

$idCiclo = (int)($_POST['idCiclo'] ?? 0);
if (!$idCiclo) {
    header("Location: ../../../vistas/admin/academico/resultadosFinales.php");
    exit;
}

$ciclo      = obtenerCicloPorId($idCiclo);
$resultados = listarResultadosFinalesCiclo($idCiclo);

if (!$ciclo || empty($resultados)) {
    $_SESSION['errores'] = "No se han encontrado notas o resultados académicos disponibles para exportar en este ciclo.";
    header("Location: ../../../vistas/admin/academico/resultadosFinales.php?idCiclo=$idCiclo");
    exit;
}

// Security: Limit export size (prevent memory exhaustion)
// Typical grades export: ~500 students × 100 bytes = 50KB, max reasonable ~10MB
$maxRowsToExport = 10000;
if (count($resultados) > $maxRowsToExport) {
    $_SESSION['errores'] = "Demasiados registros para exportar en un único archivo. Contacte al administrador.";
    header("Location: ../../../vistas/admin/academico/resultadosFinales.php?idCiclo=$idCiclo");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// GENERACIÓN DEL EXCEL
// ══════════════════════════════════════════════════════════════════════
$ss   = new Spreadsheet();
$hoja = $ss->getActiveSheet();
$hoja->setTitle('Resultados Finales');

// ── Fila de cabeceras ──
// El "(75%/25%)" solo es cierto con la fórmula heredada; con el motor
// configurable (feature_academico_config) los pesos los define cada centro.
$sufijoModulos = motorAcademicoActivo() ? '' : ' (75%)';
$sufijoRetos   = motorAcademicoActivo() ? '' : ' (25%)';
$cabeceras = ['Estudiante', 'Media Módulos' . $sufijoModulos, 'Media Retos' . $sufijoRetos, 'Nota TFG', 'Nota Final', 'Estado'];
$col = 'A';
foreach ($cabeceras as $cab) {
    $hoja->setCellValue($col . '1', $cab);
    $col++;
}

$estilo_cabecera = [
    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0EA5E9']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFB0D4E8']]],
];
$hoja->getStyle('A1:F1')->applyFromArray($estilo_cabecera);

// ── Filas de datos ──
// Security: Prevent formula injection (= + - @) in cell values
$sanitizeForExcel = function($value) {
    if (!is_string($value)) return $value;
    $value = (string)$value;
    if (strlen($value) === 0) return $value;
    $firstChar = $value[0];
    if (in_array($firstChar, ['=', '+', '-', '@'], true)) {
        return "'" . $value; // Excel: single quote forces text, blocks formula
    }
    return $value;
};

$fila = 2;
foreach ($resultados as $r) {
    $hoja->setCellValue('A' . $fila, $sanitizeForExcel($r['nombreEstudiante']));
    $hoja->setCellValue('B' . $fila, $r['media_modulos']);
    $hoja->setCellValue('C' . $fila, $r['media_retos']);
    $hoja->setCellValue('D' . $fila, $r['nota_tfg'] ?? '—');
    $hoja->setCellValue('E' . $fila, $r['promedio_global']);
    $hoja->setCellValue('F' . $fila, $sanitizeForExcel($r['estado_global']));

    $colorFondo = match($r['estado_global']) {
        'APROBADO'  => 'FFD1FAE5',
        'SUSPENSO'  => 'FFFEE2E2',
        default     => 'FFFFF9C4',
    };
    $hoja->getStyle("A{$fila}:F{$fila}")->getFill()
         ->setFillType(Fill::FILL_SOLID)
         ->getStartColor()->setARGB($colorFondo);
    $fila++;
}

// ── Anchos de columna ──
foreach (['A' => 32, 'B' => 22, 'C' => 20, 'D' => 12, 'E' => 14, 'F' => 14] as $c => $w) {
    $hoja->getColumnDimension($c)->setWidth($w);
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA — Descarga del archivo
// ══════════════════════════════════════════════════════════════════════
$nombreCiclo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $ciclo['nombreCiclo']);
$filename    = 'resultados_' . $nombreCiclo . '_' . date('Y-m-d') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($ss);
$writer->save('php://output');
exit;

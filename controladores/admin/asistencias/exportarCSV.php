<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";

$idCiclo      = (int)($_GET['idCiclo']    ?? 0) ?: null;
$idModulo     = (int)($_GET['idModulo']   ?? 0) ?: null;
$idEstudiante = (int)($_GET['idEstudiante'] ?? 0) ?: null;
$rawDesde  = $_GET['fechaDesde'] ?? '';
$rawHasta  = $_GET['fechaHasta'] ?? '';
$fechaDesde = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDesde) ? $rawDesde : null;
$fechaHasta = preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawHasta) ? $rawHasta : null;
$estadosPermitidos = ['presente', 'ausente', 'retraso', 'justificado'];
$estado = in_array($_GET['estado'] ?? '', $estadosPermitidos, true) ? $_GET['estado'] : null;

$asistencias = listarAsistenciasFiltradas($idCiclo, $idModulo, $idEstudiante, $fechaDesde, $fechaHasta, $estado);

$filename = 'asistencias_' . date('Y-m-d') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');

$fh = fopen('php://output', 'w');
fprintf($fh, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM for Excel
fputcsv($fh, ['Fecha', 'Estudiante', 'Módulo', 'Ciclo', 'Estado', 'Observación']);

$estados = [
    'presente'    => 'Presente',
    'ausente'     => 'Ausente',
    'retraso'     => 'Retraso',
    'justificado' => 'Justificado',
];

foreach ($asistencias as $asistencia) {
    fputcsv($fh, [
        date('d/m/Y', strtotime($asistencia['fecha'])),
        $asistencia['nombreEstudiante'],
        $asistencia['nombreModulo'],
        $asistencia['nombreCiclo'],
        $estados[$asistencia['estado']] ?? $asistencia['estado'],
        $asistencia['observacion'] ?? '',
    ]);
}

fclose($fh);
exit;

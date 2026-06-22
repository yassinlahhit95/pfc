<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";

$idCiclo      = (int)($_GET['idCiclo']    ?? 0) ?: null;
$idModulo     = (int)($_GET['idModulo']   ?? 0) ?: null;
$idEstudiante = (int)($_GET['idEstudiante'] ?? 0) ?: null;
$fechaDesde   = $_GET['fechaDesde'] ?? null;
$fechaHasta   = $_GET['fechaHasta'] ?? null;

$asistencias = listarAsistenciasFiltradas($idCiclo, $idModulo, $idEstudiante, $fechaDesde, $fechaHasta);

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

foreach ($asistencias as $a) {
    fputcsv($fh, [
        date('d/m/Y', strtotime($a['fecha'])),
        $a['nombreEstudiante'],
        $a['nombreModulo'],
        $a['nombreCiclo'],
        $estados[$a['estado']] ?? $a['estado'],
        $a['observacion'] ?? '',
    ]);
}

fclose($fh);
exit;

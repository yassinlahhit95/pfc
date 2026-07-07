<?php
require_once __DIR__ . '/include/ReportService.php';
require_once __DIR__ . '/modelos/horarios.php';
require_once __DIR__ . '/modelos/ciclos.php';
require_once __DIR__ . '/modelos/configuracion.php';

$idCiclo = 1;
$cfg     = obtenerConfiguracionCentro();
$ciclo   = obtenerCicloPorId($idCiclo);
$celdas  = listarHorarioPorCiclo($idCiclo);
$franjas = obtenerFranjasHorario($idCiclo);
$dias    = obtenerDiasHorario();

try {
    $reportService = new ReportService();
    $reportService->generateHorario($cfg, $ciclo, $celdas, $franjas, $dias);
    $reportService->stream('horario.pdf');
    echo "SUCCESS\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

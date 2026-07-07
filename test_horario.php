<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['idCiclo'] = 1;
require_once 'c:/laragon/www/pfc/config/Config.php';

require_once 'c:/laragon/www/pfc/include/ReportService.php';
require_once 'c:/laragon/www/pfc/modelos/conectar.php';
require_once 'c:/laragon/www/pfc/modelos/horarios.php';
require_once 'c:/laragon/www/pfc/modelos/ciclos.php';
require_once 'c:/laragon/www/pfc/modelos/configuracion.php';

$cfg     = obtenerConfiguracionCentro();
$ciclo   = obtenerCicloPorId(1);
$celdas  = listarHorarioPorCiclo(1);
$franjas = obtenerFranjasHorario(1);
$dias    = obtenerDiasHorario();

try {
    $reportService = new ReportService();
    $reportService->generateHorario($cfg, $ciclo, $celdas, $franjas, $dias);
    echo "PDF generated successfully\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

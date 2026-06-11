<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';

$_proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_dir   = dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))));
if ($_dir === '/' || $_dir === '\\' || $_dir === '.') $_dir = '';
$_back  = $_proto . '://' . $_SERVER['HTTP_HOST'] . $_dir . '/vistas/admin/informes/informes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

$_vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($_vendor)) {
    $_SESSION['errores'] = "Error: la carpeta vendor/ no está disponible en el servidor.";
    header("Location: $_back"); exit;
}

require_once $_vendor;
require_once __DIR__ . '/../../../include/ReportService.php';
require_once __DIR__ . '/../../../modelos/horarios.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

$idCiclo = (int)($_POST['idCiclo'] ?? 0);
if (!$idCiclo) { header("Location: $_back"); exit; }

$cfg    = obtenerConfiguracionCentro();
$ciclo  = obtenerCicloPorId($idCiclo);
$celdas = listarHorarioPorCiclo($idCiclo);
$franjas = obtenerFranjasHorario($idCiclo);
$dias    = obtenerDiasHorario();

$reportService = new ReportService();
$reportService->generateHorario($cfg, $ciclo, $celdas, $franjas, $dias);

$filename = 'horario_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
$reportService->stream($filename);

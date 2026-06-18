<?php
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . '/../../../config/Config.php';

$_back = rtrim(Config::getInstance()->get('APP_URL', ''), '/') . '/vistas/estudiantes/horario/horario.php';

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
require_once __DIR__ . '/../../../modelos/estudiantes.php';

// Derive idCiclo from the student's own session — no POST input needed
$datosEst = obtenerEstudiantePorId((int)$_SESSION['idEstudiante']);
$idCiclo  = (int)($datosEst['idCiclo'] ?? 0);
if (!$idCiclo) {
    $_SESSION['errores'] = "No tienes un ciclo asignado.";
    header("Location: $_back"); exit;
}

$cfg     = obtenerConfiguracionCentro();
$ciclo   = obtenerCicloPorId($idCiclo);
$celdas  = listarHorarioPorCiclo($idCiclo);
$franjas = obtenerFranjasHorario($idCiclo);
$dias    = obtenerDiasHorario();

$reportService = new ReportService();
$reportService->generateHorario($cfg, $ciclo, $celdas, $franjas, $dias);

$filename = 'horario_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
$reportService->stream($filename);

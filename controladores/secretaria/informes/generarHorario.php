<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../config/Config.php';

$_back = rtrim(Config::getInstance()->get('APP_URL', ''), '/') . '/vistas/secretaria/informes/informes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

// El guard ya validó con rotate=false; esta segunda comprobación tampoco debe rotar,
// o generar el PDF (target="_blank") consumiría el token y rompería el AJAX del editor de horario.
if (!Security::validateCSRFToken(null, false)) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

$_vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($_vendor)) {
    $_SESSION['errores'] = "Error interno del servidor al generar el informe. Contacte con el soporte técnico.";
    header("Location: $_back"); exit;
}

require_once $_vendor;
require_once __DIR__ . '/../../../include/ReportService.php';
require_once __DIR__ . '/../../../modelos/horarios.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idCiclo = (int)($_POST['idCiclo'] ?? 0);
if (!$idCiclo) { header("Location: $_back"); exit; }

$cfg     = obtenerConfiguracionCentro();
$ciclo   = obtenerCicloPorId($idCiclo);
$celdas  = listarHorarioPorCiclo($idCiclo);
$franjas = obtenerFranjasHorario($idCiclo);
$dias    = obtenerDiasHorario();

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
try {
    $reportService = new ReportService();
    $reportService->generateHorario($cfg, $ciclo, $celdas, $franjas, $dias);
    $filename = 'horario_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
    $reportService->stream($filename);
} catch (\Throwable $e) {
    error_log('generarHorario error: ' . $e->getMessage());
    $_SESSION['errores'] = 'No fue posible generar el PDF del horario. Por favor, inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

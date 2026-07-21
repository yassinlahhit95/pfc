<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_horario');
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../modelos/tutores.php';

$_appUrl = rtrim(Config::getInstance()->get('APP_URL', ''), '/');
$_dashboard = $_appUrl . '/vistas/tutores/inicio/dashboard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_dashboard"); exit;
}

// Solo se permite generar el horario de un hijo propio, nunca un id arbitrario llegado por POST.
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$hijos = listarEstudiantesPorTutor((int)$_SESSION['idTutor']);
$datosEst = null;
foreach ($hijos as $hijo) {
    if ((int)$hijo['idEstudiante'] === $idEstudiante) {
        $datosEst = $hijo;
        break;
    }
}

$_back = $_appUrl . '/vistas/tutores/horario/horario.php?id=' . $idEstudiante;

if (!$datosEst) {
    $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
    header("Location: $_dashboard"); exit;
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

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idCiclo = (int)($datosEst['idCiclo'] ?? 0);
if (!$idCiclo) {
    $_SESSION['errores'] = "El estudiante no tiene un ciclo asignado.";
    header("Location: $_back"); exit;
}

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
    error_log('generarHorario (tutor): ' . $e->getMessage());
    $_SESSION['errores'] = "No se pudo generar el horario en PDF. Inténtalo de nuevo.";
    header("Location: $_back"); exit;
}

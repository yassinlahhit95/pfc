<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../config/Config.php';

$_back = rtrim(Config::getInstance()->get('APP_URL', ''), '/') . '/vistas/admin/informes/informes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

$_vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($_vendor)) {
    $_SESSION['errores'] = "Error interno del servidor al generar el listado. Contacte con el soporte técnico.";
    header("Location: $_back"); exit;
}

require_once $_vendor;
require_once __DIR__ . '/../../../include/ReportService.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idCiclo       = (int)($_POST['idCiclo']   ?? 0);
$estudianteIds = $_POST['estudiantes'] ?? [];
$cfg           = obtenerConfiguracionCentro();

if ($idCiclo) {
    $ciclo       = obtenerCicloPorId($idCiclo);
    $estudiantes = listarEstudiantesPorCiclo($idCiclo);
} else {
    $ciclo       = ['nombreCiclo' => 'Todos los Ciclos', 'abreviaturaCiclo' => 'TODOS'];
    $estudiantes = listarEstudiantes();
}

if (!empty($estudianteIds)) {
    $estudiantes = array_filter($estudiantes, function($e) use ($estudianteIds) {
        return in_array($e['idEstudiante'], $estudianteIds);
    });
}

if (empty($estudiantes)) {
    $_SESSION['errores'] = "No hay alumnos seleccionados para generar el listado.";
    header("Location: $_back"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
try {
    $reportService = new ReportService();
    $reportService->generateListado($cfg, $ciclo, $estudiantes);
    $filename = 'listado_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
    $reportService->stream($filename);
} catch (\Throwable $e) {
    error_log('generarListado error: ' . $e->getMessage());
    $_SESSION['errores'] = 'No fue posible generar el PDF del listado. Por favor, inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

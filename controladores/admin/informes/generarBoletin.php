<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../config/Config.php';

$_base = rtrim(Config::getInstance()->get('APP_URL', ''), '/');
$_back = $_base . '/vistas/admin/informes/informes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

$_vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($_vendor)) {
    $_SESSION['errores'] = "Error interno del servidor. Contacta con el administrador del sistema.";
    header("Location: $_back"); exit;
}

require_once $_vendor;
require_once __DIR__ . '/../../../config/Config.php';
require_once __DIR__ . '/../../../include/ReportService.php';
require_once __DIR__ . '/../../../modelos/calificaciones.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

$_secret = Config::getInstance()->get('BOLETIN_SECRET');
if (empty($_secret)) {
    $_SESSION['errores'] = "Error de configuración del servidor. Contacta con el administrador.";
    header("Location: $_back"); exit;
}

$idCiclo       = (int)($_POST['idCiclo'] ?? 0);
$estudianteIds = $_POST['estudiantes'] ?? [];

if (!$idCiclo) { header("Location: $_back"); exit; }

$cfg    = obtenerConfiguracionCentro();
$ciclo  = obtenerCicloPorId($idCiclo);
$datos  = generarDatosBoletinCiclo($idCiclo);

if (empty($datos['estudiantes'])) {
    $_SESSION['errores'] = "No hay estudiantes en este ciclo.";
    header("Location: $_back"); exit;
}

$estudiantesAFiltro = $datos['estudiantes'];
if (!empty($estudianteIds)) {
    $estudiantesAFiltro = array_filter($estudiantesAFiltro, function($e) use ($estudianteIds) {
        return in_array($e['idEstudiante'], $estudianteIds);
    });
}

if (empty($estudiantesAFiltro)) {
    $_SESSION['errores'] = "No has seleccionado ningún estudiante válido.";
    header("Location: $_back"); exit;
}

foreach ($estudiantesAFiltro as &$est) {
    // HMAC-SHA256: separator prevents id=1,ciclo=23 ≡ id=12,ciclo=3
    $serialRaw = hash_hmac('sha256',
        ($est['idEstudiante'] ?? '') . '|' . ($ciclo['idCiclo'] ?? '') . '|' . date('Y'),
        $_secret
    );
    $serial = 'BLT-' . date('Y') . '-' .
        strtoupper(substr($serialRaw, 0, 4)) . '-' .
        strtoupper(substr($serialRaw, 4, 4)) . '-' .
        strtoupper(substr($serialRaw, 8, 4)) . '-' .
        strtoupper(substr($serialRaw, 12, 4));
    $est['_serial'] = $serial;

    guardarBoletinLog(
        $serial,
        (int)$est['idEstudiante'],
        $idCiclo,
        $est['nombreEstudiante'] ?? '',
        $ciclo['nombreCiclo']    ?? '',
        $cfg['cursoEscolar']     ?? date('Y')
    );
}
unset($est);

try {
    $reportService = new ReportService();
    $reportService->generateBoletines($cfg, $ciclo, $estudiantesAFiltro, $_base);
    $filename = 'boletines_' . preg_replace('/\W+/', '_', $ciclo['abreviaturaCiclo'] ?? 'ciclo') . '_' . date('Ymd') . '.pdf';
    $reportService->stream($filename);
} catch (\Throwable $e) {
    error_log('generarBoletin error: ' . $e->getMessage());
    $_SESSION['errores'] = 'No se pudo generar el PDF. Inténtalo de nuevo.';
    header("Location: $_back"); exit;
}

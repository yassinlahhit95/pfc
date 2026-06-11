<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';

// Absolute base path — works locally (/pfc) and on server (/)
$_proto    = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_dir      = dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))));
if ($_dir === '/' || $_dir === '\\' || $_dir === '.') $_dir = '';
$_base     = $_proto . '://' . $_SERVER['HTTP_HOST'] . $_dir;
$_back     = $_base . '/vistas/admin/informes/informes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

$_vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($_vendor)) {
    $_SESSION['errores'] = "Error: la carpeta vendor/ no está disponible en el servidor. Ejecuta 'composer install' y sube la carpeta vendor/.";
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
    $_SESSION['errores'] = "Error de configuración: BOLETIN_SECRET no definido en .env";
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
    $_SESSION['errores'] = 'Error al generar el PDF: ' . $e->getMessage();
    header("Location: $_back"); exit;
}

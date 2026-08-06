<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_informes');
require_once __DIR__ . '/../../../config/Config.php';

$_base = rtrim(Config::getInstance()->get('APP_URL', ''), '/');
$_back = $_base . '/vistas/estudiantes/calificaciones/lista.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

// El guard ya validó con rotate=false; esta segunda comprobación tampoco debe rotar,
// o generar el PDF (target="_blank") consumiría el token sin recargar la página.
if (!Security::validateCSRFToken(null, false)) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

$_vendor = __DIR__ . '/../../../vendor/autoload.php';
if (!file_exists($_vendor)) {
    $_SESSION['errores'] = "Error interno del servidor al generar el boletín. Contacte con el soporte técnico.";
    header("Location: $_back"); exit;
}

require_once $_vendor;
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../include/ReportService.php';
require_once __DIR__ . '/../../../modelos/calificaciones.php';
require_once __DIR__ . '/../../../modelos/ciclos.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$_secret = Config::getInstance()->get('BOLETIN_SECRET');
if (empty($_secret)) {
    $_SESSION['errores'] = "Error de configuración de seguridad del servidor. Contacte con el administrador del sistema.";
    header("Location: $_back"); exit;
}

// Autoservicio: siempre el propio estudiante de la sesión, nunca un id llegado por POST.
$idEstudiante = (int)$_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = (int)($estudianteActual['idCiclo'] ?? 0);

if (!$idCiclo) {
    $_SESSION['errores'] = "No tienes un ciclo formativo asignado.";
    header("Location: $_back"); exit;
}

$cfg   = obtenerConfiguracionCentro();
$ciclo = obtenerCicloPorId($idCiclo);
$datos = generarDatosBoletinCiclo($idCiclo);

$estudiantesAFiltro = array_filter($datos['estudiantes'] ?? [], function ($e) use ($idEstudiante) {
    return (int)$e['idEstudiante'] === $idEstudiante;
});

if (empty($estudiantesAFiltro)) {
    $_SESSION['errores'] = "No se han encontrado calificaciones registradas para generar tu boletín.";
    header("Location: $_back"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
foreach ($estudiantesAFiltro as &$est) {
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
}
unset($est);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
try {
    $reportService = new ReportService();
    $reportService->generateBoletines($cfg, $ciclo, $estudiantesAFiltro, $_base);

    foreach ($estudiantesAFiltro as $est) {
        guardarBoletinLog(
            $est['_serial'],
            (int)$est['idEstudiante'],
            $idCiclo,
            $est['nombreEstudiante'] ?? '',
            $ciclo['nombreCiclo']    ?? '',
            $cfg['cursoEscolar']     ?? date('Y')
        );
    }

    $filename = 'boletin_' . preg_replace('/\W+/', '_', $estudianteActual['nombreEstudiante'] ?? 'estudiante') . '_' . date('Ymd') . '.pdf';
    $reportService->stream($filename);
} catch (\Throwable $e) {
    error_log('generarBoletin (estudiante) error: ' . $e->getMessage());
    $_SESSION['errores'] = 'No fue posible generar el archivo PDF de tu boletín. Por favor, inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

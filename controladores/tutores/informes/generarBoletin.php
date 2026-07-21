<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_informes');
require_once __DIR__ . '/../../../config/Config.php';

$_base = rtrim(Config::getInstance()->get('APP_URL', ''), '/');
$_back = $_base . '/vistas/tutores/inicio/dashboard.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

if (!Security::validateCSRFToken()) {
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
require_once __DIR__ . '/../../../modelos/tutores.php';

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$_secret = Config::getInstance()->get('BOLETIN_SECRET');
if (empty($_secret)) {
    $_SESSION['errores'] = "Error de configuración de seguridad del servidor. Contacte con el administrador del sistema.";
    header("Location: $_back"); exit;
}

// Solo se permite generar el boletín de un hijo propio, nunca un id arbitrario llegado por POST.
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$hijos = listarEstudiantesPorTutor((int)$_SESSION['idTutor']);
$estudianteActual = null;
foreach ($hijos as $hijo) {
    if ((int)$hijo['idEstudiante'] === $idEstudiante) {
        $estudianteActual = $hijo;
        break;
    }
}

if (!$estudianteActual) {
    $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
    header("Location: $_back"); exit;
}

$idCiclo = (int)($estudianteActual['idCiclo'] ?? 0);
if (!$idCiclo) {
    $_SESSION['errores'] = "El estudiante no tiene un ciclo formativo asignado.";
    header("Location: $_back"); exit;
}

$cfg   = obtenerConfiguracionCentro();
$ciclo = obtenerCicloPorId($idCiclo);
$datos = generarDatosBoletinCiclo($idCiclo);

$estudiantesAFiltro = array_filter($datos['estudiantes'] ?? [], function ($e) use ($idEstudiante) {
    return (int)$e['idEstudiante'] === $idEstudiante;
});

if (empty($estudiantesAFiltro)) {
    $_SESSION['errores'] = "No se han encontrado calificaciones registradas para generar el boletín.";
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
    error_log('generarBoletin (tutor) error: ' . $e->getMessage());
    $_SESSION['errores'] = 'No fue posible generar el archivo PDF del boletín. Por favor, inténtelo de nuevo.';
    header("Location: $_back"); exit;
}

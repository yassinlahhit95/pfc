<?php
// Aplica una plantilla predefinida: reemplaza el borrador completo (POST clásico).
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../include/landing/plantillas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isset($_POST['onboarding'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/landing/plantillas.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    if (isset($_POST['onboarding'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/landing/plantillas.php");
    exit;
}

$slug      = $_POST['plantilla'] ?? '';
$plantilla = landing_obtener_plantilla($slug);
if ($plantilla === null) {
    if (isset($_POST['onboarding'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Plantilla no reconocida.']);
        exit;
    }
    $_SESSION['errores'] = 'Plantilla no reconocida.';
    header("Location: ../../../vistas/admin/landing/plantillas.php");
    exit;
}

$secciones = [];
foreach (landing_secciones_de_plantilla($slug) as $seccion) {
    $limpio = landing_sanear_contenido($seccion['tipo'], $seccion['contenido']);
    if (is_string($limpio)) {
        error_log("Error applying template section {$seccion['tipo']}: $limpio");
        continue;
    }
    $secciones[] = ['tipo' => $seccion['tipo'], 'contenido' => $limpio];
}

if (!$secciones || !reemplazarBorradorLanding($slug, $secciones)) {
    if (isset($_POST['onboarding'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'No se pudo aplicar la plantilla.']);
        exit;
    }
    $_SESSION['errores'] = 'No se pudo aplicar la plantilla.';
    header("Location: ../../../vistas/admin/landing/plantillas.php");
    exit;
}

// El color de acento de la plantilla pasa a los ajustes del borrador
$cfg     = obtenerLandingConfig();
$ajustes = json_decode($cfg['ajustes'] ?? '', true) ?: [];
$ajustes['colorAcento'] = $plantilla['colorAcento'];
guardarAjustesLanding($slug, json_encode($ajustes, JSON_UNESCAPED_UNICODE));

registrarAccion('actualizar', 'landing', null, 'Plantilla «' . $slug . '» aplicada al borrador');

if (isset($_POST['onboarding'])) {
    // También publicamos el borrador automáticamente al finalizar el onboarding
    publicarLanding();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'msg' => 'Plantilla aplicada y publicada.']);
    exit;
}

$_SESSION['exito'] = 'Plantilla «' . $plantilla['nombre'] . '» aplicada al borrador. Revisa el resultado y pulsa «Publicar» cuando esté listo.';
header("Location: ../../../vistas/admin/landing/builder.php");
exit;

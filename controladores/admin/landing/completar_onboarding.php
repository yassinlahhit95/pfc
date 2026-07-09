<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../include/landing/plantillas.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

$slug = $_POST['plantilla'] ?? '';
$plantilla = landing_obtener_plantilla($slug);
if ($plantilla === null) {
    echo json_encode(['ok' => false, 'msg' => 'Plantilla no reconocida.']);
    exit;
}

// 1. Guardar datos del centro
$datosCentro = [
    'nombreCentro'            => trim($_POST['nombreCentro'] ?? ''),
    'emailCentro'             => trim($_POST['emailCentro'] ?? ''),
    'telefonoCentro'          => trim($_POST['telefonoCentro'] ?? ''),
    'ciudadCentro'            => trim($_POST['ciudadCentro'] ?? ''),
    'codigoCentro'            => trim($_POST['codigoCentro'] ?? ''),
    'direccionCentro'         => trim($_POST['direccionCentro'] ?? ''),
    'cpCentro'                => trim($_POST['cpCentro'] ?? ''),
    'cursoEscolar'            => date('Y') . '-' . (date('Y') + 1),
    'textoLegal'              => '',
    'nombreDirectorFirmante'  => trim($_POST['nombreDirectorFirmante'] ?? '')
];

$errores = [];
if (empty($datosCentro['nombreCentro'])) {
    $errores['nombreCentro'] = 'El nombre del centro es obligatorio.';
}
if (empty($datosCentro['emailCentro'])) {
    $errores['emailCentro'] = 'El correo es obligatorio.';
} elseif (!filter_var($datosCentro['emailCentro'], FILTER_VALIDATE_EMAIL)) {
    $errores['emailCentro'] = 'El correo electrónico no es válido.';
}
if (empty($datosCentro['telefonoCentro'])) {
    $errores['telefonoCentro'] = 'El teléfono es obligatorio.';
}

if (!empty($errores)) {
    echo json_encode(['ok' => false, 'errores' => $errores]);
    exit;
}

guardarConfiguracionCentro($datosCentro);

// 2. Procesar subida de logo si se adjuntó
if (isset($_FILES['logoCentro']) && $_FILES['logoCentro']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['logoCentro'];
    $uploadDir  = __DIR__ . '/../../../public/uploads/configuracion/';
    $mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    
    if ($file['size'] <= 2 * 1024 * 1024 && isset($mimeExtMap[$mime])) {
        $ext = $mimeExtMap[$mime];
        $filename = 'logoCentro_' . bin2hex(random_bytes(6)) . '.' . $ext;
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            actualizarLogoCentro('logoCentro', $filename);
        }
    }
}

// 3. Aplicar plantilla al borrador y publicar
$secciones = [];
foreach (landing_secciones_de_plantilla($slug) as $seccion) {
    $limpio = landing_sanear_contenido($seccion['tipo'], $seccion['contenido']);
    if (is_string($limpio)) {
        continue;
    }
    $secciones[] = ['tipo' => $seccion['tipo'], 'contenido' => $limpio];
}

if ($secciones && reemplazarBorradorLanding($slug, $secciones)) {
    // El color de acento de la plantilla pasa a los ajustes
    $cfg = obtenerLandingConfig();
    $ajustes = json_decode($cfg['ajustes'] ?? '', true) ?: [];
    $ajustes['colorAcento'] = $plantilla['colorAcento'];
    guardarAjustesLanding($slug, json_encode($ajustes, JSON_UNESCAPED_UNICODE));
    
    // Publicar la landing
    publicarLanding();
    
    registrarAccion('actualizar', 'landing', null, "Onboarding completado con plantilla $slug");
    echo json_encode(['ok' => true, 'msg' => 'Configuración de onboarding completada con éxito.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al aplicar la plantilla seleccionada.']);
}
exit;

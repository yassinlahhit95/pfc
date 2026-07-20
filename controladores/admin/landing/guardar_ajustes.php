<?php
// Guarda los ajustes globales del borrador: color, SEO y redes sociales (AJAX JSON).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

if (!Security::validateCSRFToken(null, false)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
    exit;
}

$color = trim($_POST['colorAcento'] ?? '');
if ($color !== '' && !preg_match('/^#[0-9a-f]{6}$/i', $color)) $color = '';

$redes = [];
$redesPermitidas = ['facebook', 'instagram', 'twitter', 'linkedin', 'youtube', 'tiktok'];
foreach ($redesPermitidas as $red) {
    $url = trim($_POST['red_' . $red] ?? '');
    if ($url !== '' && preg_match('#^https?://#i', $url)) {
        $redes[$red] = mb_substr(strip_tags($url), 0, 255);
    }
}

$ajustes = [
    'colorAcento'    => strtolower($color),
    'tituloSeo'      => mb_substr(trim(strip_tags($_POST['tituloSeo'] ?? '')), 0, 150),
    'descripcionSeo' => mb_substr(trim(strip_tags($_POST['descripcionSeo'] ?? '')), 0, 300),
    'mostrarTopbar'  => !empty($_POST['mostrarTopbar']) ? 'si' : 'no',
    'redes'          => $redes,
];

$cfg = obtenerLandingConfig();
$ok  = guardarAjustesLanding($cfg['plantilla'], json_encode($ajustes, JSON_UNESCAPED_UNICODE));
if ($ok) {
    registrarAccion('actualizar', 'landing', null, 'Ajustes de la landing guardados');
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => 'Ajustes guardados.']
    : ['ok' => false, 'msg' => 'No se pudieron guardar los ajustes.']);

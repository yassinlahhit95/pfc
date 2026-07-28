<?php
// Cabecera del documento de la landing pública.
// Espera: $cfg, $ajustes, $tema (slug whitelisted), $preview
// Computed early (not just for the JSON-LD block below) so <base> can use it
// too — the preview iframe injects this document via `srcdoc` (see
// landing-builder.js's recargarPreview(), needed to dodge X-Frame-Options/CSP
// framing restrictions on a normal <iframe src>), which makes every relative
// URL in this document resolve against the PARENT page's URL
// (/vistas/admin/landing/builder.php) instead of the site root — breaking
// every relative CSS/JS/link in this template (base.css, tema-*.css,
// landing-system/assets/js/landing.js, favicon, nav/footer links, all of
// them written relative-to-root). <base> pins the resolution root back to
// the real site regardless of where the srcdoc happens to be embedded.
$_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$_esquemaSitio = $_secure ? 'https://' : 'http://';
$_urlSitio     = $_esquemaSitio . ($_SERVER['HTTP_HOST'] ?? '');

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$subFolder = '';
if (strpos($scriptName, '/') !== false) {
    $parts = explode('/', trim($scriptName, '/'));
    array_pop($parts); // Remove the script name (e.g., 'index.php')
    if (!empty($parts)) {
        $subFolder = '/' . implode('/', $parts);
    }
}
$_urlSitio .= $subFolder;



$plantillaMeta = landing_obtener_plantilla($tema) ?? [];
$acento        = $ajustes['colorAcento'] ?? '';
if (!preg_match('/^#[0-9a-f]{6}$/i', $acento)) {
    $acento = $plantillaMeta['colorAcento'] ?? '#1d4ed8';
}
$tituloSeo = trim($ajustes['tituloSeo'] ?? '') ?: ($cfg['nombreCentro'] . ' — Formación Profesional');
$descSeo   = trim($ajustes['descripcionSeo'] ?? '') ?: ('Centro de Formación Profesional. Ciclos formativos oficiales, admisiones online y prácticas en empresas. ' . $cfg['nombreCentro'] . '.');
require_once __DIR__ . '/../../../include/R2Client.php';
$logoUrl = '';
if (!empty($cfg['logoCentro'])) {
    $logoFichero = basename($cfg['logoCentro']);
    $logoUrl = R2Client::imagenUrl(
        __DIR__ . '/../../../public/uploads/configuracion/' . $logoFichero,
        'public/uploads/configuracion/' . $logoFichero,
        'configuracion/' . $logoFichero
    );
}
$logoUrlAbsoluta = $logoUrl !== '' && preg_match('#^https?://#i', $logoUrl);

// Plus Jakarta Sans es la fuente de cuerpo en todos los temas (base.css --lp-fuente)
// y también la de titulares en "institucional" y "universidad", así que se
// carga siempre. Sora/Lora solo se piden para los temas que realmente los
// usan como --lp-fuente-titulos, para no bloquear el render con familias de
// letra que la plantilla no consume.
$fuentesTitularesPorTema = [
    'institucional' => '', // reutiliza Plus Jakarta Sans, ya cargada
    'clasico'       => 'family=Lora:wght@500;600;700',
    'vocacional'    => 'family=Sora:wght@400;600;700;800',
    'universidad'   => '', // reutiliza Plus Jakarta Sans, ya cargada
];
$fuenteTitulares = $fuentesTitularesPorTema[$tema] ?? 'family=Sora:wght@400;600;700;800';
$googleFontsUrl = 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800'
    . ($fuenteTitulares !== '' ? '&' . $fuenteTitulares : '')
    . '&display=swap';

// Datos estructurados EducationalOrganization — Google recomienda incluir
// esta marca en todas las páginas del sitio, no solo en la home.
$orgSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'EducationalOrganization',
    'name'        => $cfg['nombreCentro'] ?? '',
    'url'         => $_urlSitio,
    'description' => $descSeo,
];
if ($logoUrl) {
    $orgSchema['logo']  = $logoUrlAbsoluta ? $logoUrl : $_urlSitio . '/' . $logoUrl;
    $orgSchema['image'] = $orgSchema['logo'];
}
if (!empty($cfg['telefonoCentro'])) $orgSchema['telephone'] = $cfg['telefonoCentro'];
if (!empty($cfg['emailCentro']))    $orgSchema['email']     = $cfg['emailCentro'];
if (!empty($cfg['direccionCentro']) || !empty($cfg['ciudadCentro'])) {
    $orgSchema['address'] = array_filter([
        '@type'           => 'PostalAddress',
        'streetAddress'   => $cfg['direccionCentro'] ?? '',
        'postalCode'      => $cfg['cpCentro'] ?? '',
        'addressLocality' => $cfg['ciudadCentro'] ?? '',
        'addressCountry'  => 'ES',
    ]);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<?php if ($preview): ?>
<base href="<?= Security::escapeHtml($_urlSitio . '/') ?>">
<?php endif; ?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title><?= Security::escapeHtml($tituloSeo) ?></title>
<meta name="description" content="<?= Security::escapeHtml($descSeo) ?>">
<?php if ($preview): ?>
<meta name="robots" content="noindex, nofollow">
<?php else: ?>
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<?php endif; ?>
<meta property="og:type" content="website">
<meta property="og:title" content="<?= Security::escapeHtml($tituloSeo) ?>">
<meta property="og:description" content="<?= Security::escapeHtml($descSeo) ?>">
<meta property="og:locale" content="es_ES">
<script type="application/ld+json"><?= json_encode($orgSchema, JSON_UNESCAPED_UNICODE) ?></script>
<link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="<?= Security::escapeHtml($googleFontsUrl) ?>" rel="stylesheet">
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
<link rel="stylesheet" href="/landing-system/temas/base.css">
<link rel="stylesheet" href="/landing-system/temas/tema-<?= Security::escapeHtml($tema) ?>.css">
<style>:root{--lp-acento:<?= Security::escapeHtml($acento) ?>;}</style>
<script>/* Tema oscuro sin parpadeo: se aplica antes del primer render */
(function(){try{if(localStorage.getItem('theme')==='dark'){document.documentElement.setAttribute('data-theme','dark');}}catch(e){}})();</script>
</head>
<body class="tema-<?= Security::escapeHtml($tema) ?>">
<?php if ($preview): ?>
<div class="lp-preview-badge"><i class="fas fa-eye"></i> Previsualización del borrador</div>
<?php endif; ?>

<?php
// Cabecera del documento de la landing pública.
// Espera: $cfg, $ajustes, $tema (slug whitelisted), $preview
$plantillaMeta = landing_obtener_plantilla($tema) ?? [];
$acento        = $ajustes['colorAcento'] ?? '';
if (!preg_match('/^#[0-9a-f]{6}$/i', $acento)) {
    $acento = $plantillaMeta['colorAcento'] ?? '#1d4ed8';
}
$tituloSeo = trim($ajustes['tituloSeo'] ?? '') ?: ($cfg['nombreCentro'] . ' — Formación Profesional');
$descSeo   = trim($ajustes['descripcionSeo'] ?? '') ?: ('Centro de Formación Profesional. Ciclos formativos oficiales, admisiones online y prácticas en empresas. ' . $cfg['nombreCentro'] . '.');
$logoUrl = '';
if (!empty($cfg['logoCentro'])) {
    $logoFichero = basename($cfg['logoCentro']);
    if (file_exists(__DIR__ . '/../../public/uploads/configuracion/' . $logoFichero)) {
        $logoUrl = '/public/uploads/configuracion/' . $logoFichero;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
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
<link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@400;600;700;800&family=Lora:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="/landing-system/temas/base.css">
<link rel="stylesheet" href="/landing-system/temas/tema-<?= Security::escapeHtml($tema) ?>.css">
<style>:root{--lp-acento:<?= Security::escapeHtml($acento) ?>;}</style>
</head>
<body class="tema-<?= Security::escapeHtml($tema) ?>">
<?php if ($preview): ?>
<div class="lp-preview-badge"><i class="fas fa-eye"></i> Previsualización del borrador</div>
<?php endif; ?>

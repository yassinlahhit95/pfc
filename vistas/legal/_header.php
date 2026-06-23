<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/configuracion.php';
require_once __DIR__ . '/../../include/FeatureGuard.php';
$cfg = obtenerConfiguracionCentro();
$nombreCentro = $cfg['nombreCentro'] ?? 'AulaPro';
$emailCentro  = $cfg['emailCentro']  ?? '';
$is_prematricula_enabled = FeatureGuard::check('feature_prematricula');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($legal_titulo ?? 'Información Legal') ?> — AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/legal.css">
    <?php if (!empty($extra_css)) foreach ((array)$extra_css as $_css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($_css) ?>">
    <?php endforeach; ?>
</head>
<body>

<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <linearGradient id="apg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#2563EB"/>
      <stop offset="1" stop-color="#1D4ED8"/>
    </linearGradient>
    <symbol id="ap-logo" viewBox="0 0 40 40">
      <rect width="40" height="40" rx="11" fill="url(#apg)"/>
      <path d="M20 8.5 L30 31 H24.7 L23 26.6 H17 L15.3 31 H10 Z M18.5 22.2 H21.5 L20 17.6 Z" fill="#fff"/>
    </symbol>
  </defs>
</svg>

<header class="legal-topbar">
    <div class="legal-topbar-inner">
        <a href="/" class="legal-brand">
            <svg class="legal-brand-logo" viewBox="0 0 40 40"><use href="#ap-logo"/></svg>
            Aula<b>Pro</b>
        </a>
        <nav class="legal-nav-links">
            <a href="/vistas/legal/aviso-legal.php"<?= ($legal_pagina ?? '') === 'aviso-legal' ? ' class="activo"' : '' ?>>Aviso Legal</a>
            <a href="/vistas/legal/politica-de-privacidad.php"<?= ($legal_pagina ?? '') === 'privacidad' ? ' class="activo"' : '' ?>>Privacidad</a>
            <a href="/vistas/legal/politica-de-cookies.php"<?= ($legal_pagina ?? '') === 'cookies' ? ' class="activo"' : '' ?>>Cookies</a>
            <a href="/vistas/legal/politica-de-gestion.php"<?= ($legal_pagina ?? '') === 'gestion' ? ' class="activo"' : '' ?>>Política de Gestión</a>
        </nav>
        <div class="legal-nav-cta">
            <a class="legal-access" href="/vistas/login.php">Acceso</a>
            <?php if ($is_prematricula_enabled): ?>
            <a class="legal-btn-primary" href="/vistas/admisiones/pre-matricula.php">Pre-matrícula</a>
            <?php endif; ?>
        </div>
    </div>
</header>

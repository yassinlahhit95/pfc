<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();
require_once __DIR__ . '/../../modelos/configuracion.php';
require_once __DIR__ . '/../../include/FeatureGuard.php';
require_once __DIR__ . '/../../include/AssetMin.php';
require_once __DIR__ . '/../../include/I18n.php';
$cfg = obtenerConfiguracionCentro();
$nombreCentro = $cfg['nombreCentro'] ?? FeatureGuard::getCenterName();
$emailCentro  = $cfg['emailCentro']  ?? '';
$prematriculaHabilitada = FeatureGuard::check('feature_prematricula');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($legal_titulo ?? 'Información Legal') ?> — <?= Security::escapeHtml($nombreCentro) ?></title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha384-/o6I2CkkWC//PSjvWC/eYN7l3xM3tJm8ZzVkCOfp//W05QcE3mlGskpoHB6XqI+B" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= AssetMin::urlAbs(__DIR__ . '/../..', '/public/css/features/legal.css') ?>">
    <?php foreach (($extra_css ?? []) as $_css):
        // Cada entrada puede ser una URL simple (recurso propio) o
        // ['url' => ..., 'integrity' => ...] para un CDN externo (SRI).
        $_cssUrl  = is_array($_css) ? ($_css['url'] ?? '') : $_css;
        $_cssIntg = is_array($_css) ? ($_css['integrity'] ?? '') : '';
        if ($_cssUrl === '') continue;
    ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($_cssUrl) ?>"<?= $_cssIntg ? ' integrity="' . htmlspecialchars($_cssIntg) . '" crossorigin="anonymous"' : '' ?>>
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

<!-- Overlay de fondo para el drawer móvil -->
<div class="legal-nav-overlay" id="legal-nav-overlay" aria-hidden="true"></div>

<header class="legal-topbar">
    <div class="legal-topbar-inner">
        <a href="/" class="legal-brand">
            <svg class="legal-brand-logo" viewBox="0 0 40 40"><use href="#ap-logo"/></svg>
            Aula<b>Pro</b>
        </a>
        
        <!-- Desktop Nav links -->
        <nav class="legal-nav-links">
            <a href="/vistas/legal/aviso-legal.php"<?= ($legal_pagina ?? '') === 'aviso-legal' ? ' class="activo"' : '' ?>><?= __('Aviso Legal', 'Aviso Legal') ?></a>
            <a href="/vistas/legal/politica-de-privacidad.php"<?= ($legal_pagina ?? '') === 'privacidad' ? ' class="activo"' : '' ?>><?= __('privacidad', 'Privacidad') ?></a>
            <a href="/vistas/legal/politica-de-cookies.php"<?= ($legal_pagina ?? '') === 'cookies' ? ' class="activo"' : '' ?>><?= __('cookies', 'Cookies') ?></a>
            <a href="/vistas/legal/politica-de-gestion.php"<?= ($legal_pagina ?? '') === 'gestion' ? ' class="activo"' : '' ?>><?= __('gestion', 'Política de Gestión') ?></a>
        </nav>
        
        <!-- Desktop CTAs + Burger Button -->
        <div class="legal-nav-cta">
            <form action="/controladores/cambiar_idioma.php" method="POST" style="margin:0; display:inline-block; margin-right: 12px;" id="legalLangForm">
                 <select name="lang" onchange="document.getElementById('legalLangForm').submit();" style="padding:5px 8px; border-radius:8px; border:1.5px solid #e2e8f0; font-size:.85rem; background:#fff; cursor:pointer; font-weight:600; color:#475569;">
                    <option value="es" <?= I18n::getLang() === 'es' ? 'selected' : '' ?>>ES</option>
                    <option value="eu" <?= I18n::getLang() === 'eu' ? 'selected' : '' ?>>EU</option>
                    <option value="ca" <?= I18n::getLang() === 'ca' ? 'selected' : '' ?>>CA</option>
                    <option value="en" <?= I18n::getLang() === 'en' ? 'selected' : '' ?>>EN</option>
                 </select>
            </form>
            <a class="legal-access" href="/vistas/login.php"><?= __('Acceso', 'Acceso') ?></a>
            <a class="legal-btn-primary" href="/vistas/admisiones/pre-matricula.php"><?= __('Pre-matrícula', 'Pre-matrícula') ?></a>
            <!-- Burger (visible only on mobile) -->
            <button class="legal-nav-burger" id="legal-nav-burger" aria-label="Abrir menú" aria-expanded="false">
                <span class="legal-nav-burger-line"></span>
                <span class="legal-nav-burger-line"></span>
                <span class="legal-nav-burger-line"></span>
            </button>
        </div>
    </div>
</header>

<!-- Mobile slide-over drawer -->
<nav class="legal-nav-movil" id="legal-nav-movil" aria-label="Menú móvil" aria-hidden="true">
    <div class="legal-nav-movil-header">
        <a href="/" class="legal-brand">
            <svg class="legal-brand-logo" viewBox="0 0 40 40"><use href="#ap-logo"/></svg>
            Aula<b>Pro</b>
        </a>
        <button class="legal-nav-movil-close" id="legal-nav-close" aria-label="Cerrar menú">
            <i class="fas fa-xmark"></i>
        </button>
    </div>
    
    <div class="legal-nav-movil-links">
        <a href="/vistas/legal/aviso-legal.php"<?= ($legal_pagina ?? '') === 'aviso-legal' ? ' class="activo"' : '' ?>><i class="fas fa-scale-balanced"></i> Aviso Legal</a>
        <a href="/vistas/legal/politica-de-privacidad.php"<?= ($legal_pagina ?? '') === 'privacidad' ? ' class="activo"' : '' ?>><i class="fas fa-user-shield"></i> Privacidad</a>
        <a href="/vistas/legal/politica-de-cookies.php"<?= ($legal_pagina ?? '') === 'cookies' ? ' class="activo"' : '' ?>><i class="fas fa-cookie-bite"></i> Cookies</a>
        <a href="/vistas/legal/politica-de-gestion.php"<?= ($legal_pagina ?? '') === 'gestion' ? ' class="activo"' : '' ?>><i class="fas fa-briefcase"></i> Política de Gestión</a>
    </div>
    
    <div class="legal-nav-movil-ctas">
        <a href="/vistas/login.php" class="legal-access-btn"><i class="fas fa-sign-in-alt"></i> Acceso Plataforma</a>
        <a href="/vistas/admisiones/pre-matricula.php" class="legal-btn-primary"><i class="fas fa-file-signature"></i> Pre-matrícula online</a>
    </div>
</nav>

<script>
(function() {
    var burger = document.getElementById('legal-nav-burger');
    var drawer = document.getElementById('legal-nav-movil');
    var overlay = document.getElementById('legal-nav-overlay');
    var closeBtn = document.getElementById('legal-nav-close');
    var isOpen = false;

    if (!burger || !drawer || !overlay || !closeBtn) return;

    function openMenu() {
        isOpen = true;
        drawer.classList.add('abierto');
        overlay.classList.add('visible');
        burger.classList.add('activo');
        burger.setAttribute('aria-expanded', 'true');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        isOpen = false;
        drawer.classList.remove('abierto');
        overlay.classList.remove('visible');
        burger.classList.remove('activo');
        burger.setAttribute('aria-expanded', 'false');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    burger.addEventListener('click', function() { isOpen ? closeMenu() : openMenu(); });
    overlay.addEventListener('click', closeMenu);
    closeBtn.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) closeMenu();
    });
})();
</script>

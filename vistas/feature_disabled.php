<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Módulo no disponible — <?php require_once __DIR__ . '/../include/FeatureGuard.php'; echo Security::escapeHtml(FeatureGuard::getCenterName()); ?></title>
  <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
  <style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Roboto',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f0e1f;overflow:hidden}
    .fondo{position:fixed;inset:0;background:linear-gradient(145deg,#0f0e1f 0%,#1e1b4b 40%,#1e3a5f 72%,#1d4ed8 100%);z-index:0}
    .orb{position:absolute;border-radius:50%;filter:blur(90px);opacity:.15;pointer-events:none}
    .orb-1{width:400px;height:400px;background:#6366f1;top:-80px;right:-80px}
    .orb-2{width:300px;height:300px;background:#3b82f6;bottom:-60px;left:-60px}
    .grid{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px);background-size:32px 32px}
    .tarjeta{position:relative;z-index:1;background:rgba(255,255,255,.07);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:48px 40px;text-align:center;max-width:460px;width:90%;animation:aparecer .6s ease forwards}
    @keyframes aparecer{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .icono{font-size:3.5rem;margin-bottom:14px;line-height:1;color:#818cf8}
    .titulo{font-size:1.4rem;font-weight:700;color:#fff;margin-bottom:10px}
    .subtitulo{font-size:.9rem;color:#94a3b8;line-height:1.7;margin-bottom:20px}
    .modulo-tag{display:inline-block;background:rgba(99,102,241,.2);border:1px solid rgba(99,102,241,.4);border-radius:8px;padding:4px 14px;font-size:.8rem;color:#a5b4fc;font-weight:600;margin-bottom:20px;letter-spacing:.04em;text-transform:uppercase}
    .info{background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.25);border-radius:12px;padding:12px 16px;font-size:.85rem;color:#93c5fd;line-height:1.6;margin-bottom:24px;text-align:left}
    .divider{border:none;border-top:1px solid rgba(255,255,255,.1);margin:20px 0}
    .boton{display:inline-block;padding:11px 28px;background:linear-gradient(135deg,#4f46e5,#4338ca);color:#fff;border-radius:10px;font-weight:600;font-size:.9rem;text-decoration:none;transition:transform .2s,box-shadow .2s;margin:4px}
    .boton:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(79,70,229,.4)}
    .boton-sec{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15)}
    .boton-sec:hover{background:rgba(255,255,255,.12);box-shadow:none}
  </style>
</head>
<body>
<div class="fondo">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="grid"></div>
</div>

<?php
$feature = $GLOBALS['_blocked_feature'] ?? 'unknown';
$label   = ucwords(str_replace(['feature_', '_'], ['', ' '], $feature));
$labels  = [
    'feature_prematricula' => 'Pre-matrícula',
    'feature_chat'         => 'Chat',
    'feature_inventario'   => 'Inventario',
    'feature_subida_tfg'   => 'Entrega de TFG',
    'feature_anuncios'     => 'Anuncios',
    'feature_eventos'      => 'Eventos',
    'feature_retos'        => 'Retos',
    'feature_mensajes'     => 'Mensajería',
    'feature_pagos'        => 'Pagos',
    'feature_gastos'       => 'Gastos',
    'feature_informes'     => 'Informes PDF',
    'feature_fct'          => 'FCT',
];
$label = $labels[$feature] ?? $label;

// Comprueba si toda la instancia está suspendida (no solo un módulo desactivado)
$isSuspended = class_exists('FeatureGuard') && FeatureGuard::isSuspended();
$suspMsg = $isSuspended ? FeatureGuard::getSuspensionMessage() : '';

// Calcula la URL de login relativa a la raíz del documento (funciona en cualquier ruta de despliegue)
$docRoot   = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$filePath  = str_replace('\\', '/', __FILE__);
$dirPath   = '/' . ltrim(str_replace($docRoot, '', dirname($filePath)), '/');
$loginUrl  = rtrim($dirPath, '/') . '/login.php';
?>

<div class="tarjeta">
<?php if ($isSuspended): ?>
  <div class="icono"><i class="fas fa-lock"></i></div>
  <div class="modulo-tag">Acceso suspendido</div>
  <h1 class="titulo">Instancia suspendida</h1>
  <p class="subtitulo">
    El acceso a esta plataforma ha sido suspendido por el proveedor del servicio.
  </p>
  <?php if ($suspMsg): ?>
  <div class="info">
    <strong>Motivo:</strong><br>
    <?= htmlspecialchars($suspMsg, ENT_QUOTES) ?>
  </div>
  <?php else: ?>
  <div class="info">
    Para más información, contacta con el soporte de la plataforma.
  </div>
  <?php endif; ?>
<?php else: ?>
  <div class="icono"><i class="fas fa-ban"></i></div>
  <div class="modulo-tag"><?= htmlspecialchars($label, ENT_QUOTES) ?></div>
  <h1 class="titulo">Módulo desactivado</h1>
  <p class="subtitulo">
    El módulo <strong><?= htmlspecialchars($label, ENT_QUOTES) ?></strong>
    ha sido desactivado por la plataforma de administración SaaS.
  </p>
  <div class="info">
    <strong>¿Por qué veo esto?</strong><br>
    Este módulo requiere una suscripción activa o ha sido deshabilitado por el proveedor del servicio.
    Para reactivarlo, contacta con el soporte de la plataforma.
  </div>
<?php endif; ?>
  <hr class="divider">
  <a href="javascript:history.back()" class="boton boton-sec">← Volver</a>
  <a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES) ?>" class="boton boton-sec">Inicio</a>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Acceso Suspendido — AulaPro</title>
  <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    *{margin:0;padding:0;box-sizing:border-box;}
    body{font-family:'Roboto',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0f0e1f;overflow:hidden;}
    .fondo{position:fixed;inset:0;background:linear-gradient(145deg,#0f0e1f 0%,#1e1b4b 40%,#2d2a80 72%,#4338ca 100%);z-index:0;}
    .orb{position:absolute;border-radius:50%;filter:blur(90px);opacity:.15;pointer-events:none;}
    .orb-1{width:500px;height:500px;background:#ef4444;top:-100px;right:-100px;}
    .orb-2{width:300px;height:300px;background:#f97316;bottom:-60px;left:-60px;}
    .grid{position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.07) 1px,transparent 1px);background-size:32px 32px;}
    .tarjeta{position:relative;z-index:1;background:rgba(255,255,255,.07);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:56px 48px;text-align:center;max-width:480px;width:90%;animation:aparecer .6s ease forwards;}
    @keyframes aparecer{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .icono{font-size:4rem;margin-bottom:16px;line-height:1;}
    .titulo{font-size:1.5rem;font-weight:700;color:#ffffff;margin-bottom:12px;}
    .subtitulo{font-size:.95rem;color:#94a3b8;line-height:1.7;margin-bottom:12px;}
    .mensaje-admin{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:14px 18px;font-size:.9rem;color:#fca5a5;margin:20px 0;line-height:1.6;text-align:left;}
    .divider{border:none;border-top:1px solid rgba(255,255,255,.1);margin:24px 0;}
    .contacto{font-size:.85rem;color:#64748b;margin-bottom:24px;}
    .boton{display:inline-block;padding:13px 32px;background:linear-gradient(135deg,#4f46e5,#4338ca);color:#fff;border-radius:12px;font-weight:600;font-size:.95rem;text-decoration:none;transition:transform .2s,box-shadow .2s;margin:4px;}
    .boton:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(79,70,229,.4);}
    .boton-sec{background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);}
    .boton-sec:hover{background:rgba(255,255,255,.12);box-shadow:none;}
  </style>
</head>
<body>
<div class="fondo">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="grid"></div>
</div>

<div class="tarjeta">
  <div class="icono">🔒</div>
  <h1 class="titulo">Acceso Suspendido</h1>
  <p class="subtitulo">
    Tu acceso a <strong>AulaPro</strong> ha sido suspendido temporalmente.<br>
    Esto ocurre cuando la suscripción está pendiente de pago o ha expirado.
  </p>

  <?php
  $msg = $_SESSION['_suspension_message'] ?? '';
  if ($msg): ?>
  <div class="mensaje-admin">
    <strong>Mensaje del administrador:</strong><br>
    <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
  </div>
  <?php endif; ?>

  <hr class="divider">
  <p class="contacto">Para reactivar el acceso, contacta con el administrador de tu centro.</p>

  <a href="<?= htmlspecialchars($_SERVER['HTTP_REFERER'] ?? '/', ENT_QUOTES) ?>" class="boton boton-sec">← Volver</a>
  <a href="/index.php" class="boton boton-sec">Inicio</a>
</div>
</body>
</html>
